<?php

declare(strict_types=1);

namespace DnsServerBundle\Service;

use DnsServerBundle\Entity\DnsQueryLog;
use DnsServerBundle\Entity\UpstreamDnsServer;
use DnsServerBundle\Enum\DnsProtocolEnum;
use DnsServerBundle\Enum\RecordType;
use DnsServerBundle\Exception\ConcurrentQueryLimitException;
use DnsServerBundle\Exception\UpstreamServerUnavailableException;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Attribute\WithMonologChannel;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use React\Datagram\Socket;
use React\Dns\Model\Message;
use React\Dns\Model\Record;
use React\Dns\Protocol\BinaryDumper;
use React\Dns\Protocol\Parser;
use React\Dns\Query\CoopExecutor;
use React\Dns\Query\Query;
use React\Dns\Query\TcpTransportExecutor;
use React\Dns\Query\TimeoutExecutor;
use React\Dns\Query\UdpTransportExecutor;
use React\EventLoop\Loop;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Autoconfigure(public: true)]
#[WithMonologChannel(channel: 'dns_server')]
class DnsQueryService
{
    private Parser $parser;

    private BinaryDumper $dumper;

    /** @var array<string, mixed> */
    private array $pendingQueries = [];
    private const MAX_CONCURRENT_QUERIES = 1000;

    public function __construct(
        private readonly UpstreamServerMatcherService $upstreamServerMatcherService,
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger,
        private readonly CacheItemPoolInterface $cache,
        private readonly HttpClientInterface $httpClient,
    ) {
        $this->parser = new Parser();
        $this->dumper = new BinaryDumper();
    }

    private function createExecutor(UpstreamDnsServer $server): CoopExecutor
    {
        $executor = match ($server->getProtocol()) {
            DnsProtocolEnum::UDP => new UdpTransportExecutor($server->getHost() . ':' . $server->getPort()),
            DnsProtocolEnum::TCP => new TcpTransportExecutor($server->getHost() . ':' . $server->getPort()),
            DnsProtocolEnum::DOH => new DnsOverHttpsExecutor($this->httpClient, $server, $this->dumper, $this->parser, $this->logger),
            DnsProtocolEnum::DOT => new DnsOverTlsExecutor($server, $this->dumper, $this->parser),
        };

        return new CoopExecutor(new TimeoutExecutor($executor, $server->getTimeout(), Loop::get()));
    }

    private function createQueryLog(string $domain, int $type, string $remoteAddress): DnsQueryLog
    {
        $queryLog = new DnsQueryLog();
        $queryLog->setDomain($domain);
        $queryLog->setQueryType(RecordType::tryFrom($type) ?? RecordType::A);
        $queryLog->setClientIp($remoteAddress);

        return $queryLog;
    }

    private function handleQuerySuccess(Message $response, Socket $server, string $remoteAddress, Message $request, DnsQueryLog $queryLog, float $startTime): void
    {
        $response->id = $request->id;
        $data = $this->dumper->toBinary($response);

        $queryLog->setResponseTime((int) ((microtime(true) - $startTime) * 1000));
        $queryLog->setResponse(base64_encode($data));

        $this->entityManager->persist($queryLog);
        $this->entityManager->flush();

        $server->send($data, $remoteAddress);
    }

    private function handleQueryFailure(\Throwable $error, Socket $server, string $remoteAddress, Message $request, DnsQueryLog $queryLog, float $startTime): void
    {
        $queryLog->setResponseTime((int) ((microtime(true) - $startTime) * 1000));
        $queryLog->setResponse('Error: ' . $error->getMessage());

        $this->entityManager->persist($queryLog);
        $this->entityManager->flush();

        $response = new Message();
        $response->id = $request->id;
        $response->qr = true;
        $response->rcode = 2; // SERVFAIL
        $server->send($this->dumper->toBinary($response), $remoteAddress);
    }

    private function getCacheKey(string $domain, int $type): string
    {
        return sprintf('dns_query_%s_%d', $domain, $type);
    }

    private function getCachedResponse(string $domain, int $type): ?Message
    {
        $cacheKey = $this->getCacheKey($domain, $type);
        $item = $this->cache->getItem($cacheKey);

        if ($item->isHit()) {
            $data = $item->get();
            if (is_array($data) && isset($data['expires'], $data['answers'], $data['authority'], $data['additional']) && $data['expires'] > time()) {
                $response = new Message();
                // PHPStan needs explicit type assertions for cached arrays
                /** @var array<Record> $answers */
                $answers = is_array($data['answers']) ? $data['answers'] : [];
                /** @var array<Record> $authority */
                $authority = is_array($data['authority']) ? $data['authority'] : [];
                /** @var array<Record> $additional */
                $additional = is_array($data['additional']) ? $data['additional'] : [];

                $response->answers = $answers;
                $response->authority = $authority;
                $response->additional = $additional;

                return $response;
            }
            $this->cache->deleteItem($cacheKey);
        }

        return null;
    }

    private function cacheResponse(string $domain, int $type, Message $response): void
    {
        if ([] === $response->answers) {
            return;
        }

        $minTtl = PHP_INT_MAX;
        foreach ($response->answers as $answer) {
            $minTtl = min($minTtl, $answer->ttl);
        }

        $cacheKey = $this->getCacheKey($domain, $type);
        $item = $this->cache->getItem($cacheKey);
        $item->set([
            'answers' => $response->answers,
            'authority' => $response->authority,
            'additional' => $response->additional,
            'expires' => time() + $minTtl,
        ]);
        $item->expiresAfter($minTtl);

        $this->cache->save($item);
    }

    public function handleQuery(string $message, string $remoteAddress, Socket $server): void
    {
        $startTime = microtime(true);

        try {
            $request = $this->parser->parseMessage($message);
            if ([] === $request->questions) {
                return;
            }

            $question = $request->questions[0];
            $domain = strtolower($question->name);
            $type = $question->type;

            $this->checkConcurrencyLimit();

            $queryKey = $this->getCacheKey($domain, $type);
            if ($this->handlePendingQuery($queryKey, $domain, $type, $server, $remoteAddress, $request, $startTime)) {
                return;
            }

            $queryLog = $this->createQueryLog($domain, $type, $remoteAddress);

            if ($this->handleCachedResponse($domain, $type, $server, $remoteAddress, $request, $queryLog, $startTime)) {
                return;
            }

            $this->executeUpstreamQuery($domain, $type, $question->class, $server, $remoteAddress, $request, $queryLog, $startTime, $queryKey);
        } catch (\Throwable $e) {
            $this->handleQueryError($e, $remoteAddress, $domain ?? null, $type ?? null, $queryKey ?? null, $startTime);
        }
    }

    private function checkConcurrencyLimit(): void
    {
        if (count($this->pendingQueries) >= self::MAX_CONCURRENT_QUERIES) {
            throw new ConcurrentQueryLimitException('Too many concurrent DNS queries');
        }
    }

    private function handlePendingQuery(string $queryKey, string $domain, int $type, Socket $server, string $remoteAddress, Message $request, float $startTime): bool
    {
        if (!isset($this->pendingQueries[$queryKey])) {
            return false;
        }

        $pendingQuery = $this->pendingQueries[$queryKey];
        if (is_object($pendingQuery) && property_exists($pendingQuery, 'promise')) {
            $promise = $pendingQuery->promise;
            if ($promise instanceof PromiseInterface) {
                $promise->then(
                    function (mixed $response) use ($server, $remoteAddress, $request, $domain, $type, $startTime): void {
                        if ($response instanceof Message) {
                            $this->handleQuerySuccess($response, $server, $remoteAddress, $request, $this->createQueryLog($domain, $type, $remoteAddress), $startTime);
                        }
                    },
                    fn (\Throwable $error) => $this->handleQueryFailure($error, $server, $remoteAddress, $request, $this->createQueryLog($domain, $type, $remoteAddress), $startTime)
                );
            }
        }

        return true;
    }

    private function handleCachedResponse(string $domain, int $type, Socket $server, string $remoteAddress, Message $request, DnsQueryLog $queryLog, float $startTime): bool
    {
        $cachedResponse = $this->getCachedResponse($domain, $type);
        if (null === $cachedResponse) {
            return false;
        }

        $this->handleQuerySuccess($cachedResponse, $server, $remoteAddress, $request, $queryLog, $startTime);

        return true;
    }

    private function executeUpstreamQuery(string $domain, int $type, int $class, Socket $server, string $remoteAddress, Message $request, DnsQueryLog $queryLog, float $startTime, string $queryKey): void
    {
        $upstreamServer = $this->upstreamServerMatcherService->findMatchingOrDefaultServer($domain);

        if (null === $upstreamServer) {
            throw new UpstreamServerUnavailableException('No upstream DNS server available for domain: ' . $domain);
        }

        $executor = $this->createExecutor($upstreamServer);

        $deferred = new Deferred();
        $this->pendingQueries[$queryKey] = (object) [
            'promise' => $deferred->promise(),
            'startTime' => $startTime,
        ];

        $executor->query(new Query($domain, $type, $class))
            ->then(
                function (Message $response) use ($domain, $type, $server, $remoteAddress, $request, $queryLog, $startTime, $deferred, $queryKey): void {
                    unset($this->pendingQueries[$queryKey]);
                    $this->cacheResponse($domain, $type, $response);
                    $this->handleQuerySuccess($response, $server, $remoteAddress, $request, $queryLog, $startTime);
                    $deferred->resolve($response);
                },
                function (\Throwable $error) use ($server, $remoteAddress, $request, $queryLog, $startTime, $deferred, $queryKey): void {
                    unset($this->pendingQueries[$queryKey]);
                    $this->handleQueryFailure($error, $server, $remoteAddress, $request, $queryLog, $startTime);
                    $deferred->reject($error);
                }
            )
        ;
    }

    private function handleQueryError(\Throwable $e, string $remoteAddress, ?string $domain, ?int $type, ?string $queryKey, float $startTime): void
    {
        $this->logger->error('DNS query handling error', [
            'remote_address' => $remoteAddress,
            'domain' => $domain,
            'type' => $type,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'duration_ms' => (int) ((microtime(true) - $startTime) * 1000),
        ]);

        if (null !== $queryKey) {
            unset($this->pendingQueries[$queryKey]);
        }
    }
}
