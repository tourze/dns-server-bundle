<?php

declare(strict_types=1);

namespace DnsServerBundle\Service;

use DnsServerBundle\Entity\DnsQueryLog;
use DnsServerBundle\Entity\UpstreamDnsServer;
use DnsServerBundle\Enum\DnsProtocolEnum;
use DnsServerBundle\Enum\RecordType;
use DnsServerBundle\Repository\UpstreamDnsServerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use React\Datagram\Socket;
use React\Dns\Model\Message;
use React\Dns\Protocol\BinaryDumper;
use React\Dns\Protocol\Parser;
use React\Dns\Query\CoopExecutor;
use React\Dns\Query\Query;
use React\Dns\Query\TcpTransportExecutor;
use React\Dns\Query\TimeoutExecutor;
use React\Dns\Query\UdpTransportExecutor;
use React\EventLoop\Loop;
use React\Promise\Deferred;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DnsQueryService
{
    private Parser $parser;
    private BinaryDumper $dumper;
    private array $pendingQueries = [];
    private const MAX_CONCURRENT_QUERIES = 1000;

    public function __construct(
        private readonly UpstreamDnsServerRepository $upstreamDnsServerRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger,
        private readonly AdapterInterface $cache,
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
            DnsProtocolEnum::DOH => new DnsOverHttpsExecutor($this->httpClient, $server, $this->dumper),
            DnsProtocolEnum::DOT => new DnsOverTlsExecutor($server, $this->dumper),
        };

        return new CoopExecutor(new TimeoutExecutor($executor, $server->getTimeout(), Loop::get()));
    }

    private function createQueryLog(string $domain, int $type, string $remoteAddress): DnsQueryLog
    {
        $queryLog = new DnsQueryLog();
        $queryLog->setDomain($domain)
            ->setQueryType(RecordType::tryFrom($type) ?? RecordType::A)
            ->setClientIp($remoteAddress);

        return $queryLog;
    }

    private function handleQuerySuccess(Message $response, Socket $server, string $remoteAddress, Message $request, DnsQueryLog $queryLog, float $startTime): void
    {
        $response->id = $request->id;
        $data = $this->dumper->toBinary($response);

        $queryLog->setResponseTime((int)((microtime(true) - $startTime) * 1000));
        $queryLog->setResponse(base64_encode($data));

        $this->entityManager->persist($queryLog);
        $this->entityManager->flush();

        $server->send($data, $remoteAddress);
    }

    private function handleQueryFailure(\Exception $error, Socket $server, string $remoteAddress, Message $request, DnsQueryLog $queryLog, float $startTime): void
    {
        $queryLog->setResponseTime((int)((microtime(true) - $startTime) * 1000));
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
            if ($data['expires'] > time()) {
                $response = new Message();
                $response->answers = $data['answers'];
                $response->authority = $data['authority'];
                $response->additional = $data['additional'];
                return $response;
            }
            $this->cache->deleteItem($cacheKey);
        }

        return null;
    }

    private function cacheResponse(string $domain, int $type, Message $response): void
    {
        if (empty($response->answers)) {
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
            'expires' => time() + $minTtl
        ]);
        $item->expiresAfter($minTtl);

        $this->cache->save($item);
    }

    public function handleQuery(string $message, string $remoteAddress, Socket $server): void
    {
        $startTime = microtime(true);

        try {
            $request = $this->parser->parseMessage($message);
            if (empty($request->questions)) {
                return;
            }

            $question = $request->questions[0];
            $domain = strtolower($question->name);
            $type = $question->type;

            // 检查并发限制
            if (count($this->pendingQueries) >= self::MAX_CONCURRENT_QUERIES) {
                throw new \RuntimeException('Too many concurrent DNS queries');
            }

            $queryKey = $this->getCacheKey($domain, $type);
            if (isset($this->pendingQueries[$queryKey])) {
                $this->pendingQueries[$queryKey]->promise->then(
                    fn(Message $response) => $this->handleQuerySuccess($response, $server, $remoteAddress, $request, $this->createQueryLog($domain, $type, $remoteAddress), $startTime),
                    fn(\Exception $error) => $this->handleQueryFailure($error, $server, $remoteAddress, $request, $this->createQueryLog($domain, $type, $remoteAddress), $startTime)
                );
                return;
            }

            $queryLog = $this->createQueryLog($domain, $type, $remoteAddress);

            // 检查缓存
            if ($cachedResponse = $this->getCachedResponse($domain, $type)) {
                $this->handleQuerySuccess($cachedResponse, $server, $remoteAddress, $request, $queryLog, $startTime);
                return;
            }

            $upstreamServer = $this->upstreamDnsServerRepository->findMatchingServer($domain)
                ?? $this->upstreamDnsServerRepository->getDefaultServer();

            $executor = $this->createExecutor($upstreamServer);

            $deferred = new Deferred();
            $this->pendingQueries[$queryKey] = (object)[
                'promise' => $deferred->promise(),
                'startTime' => $startTime
            ];

            $executor->query(new Query($domain, $type, $question->class))
                ->then(
                    function (Message $response) use ($domain, $type, $server, $remoteAddress, $request, $queryLog, $startTime, $deferred, $queryKey) {
                        unset($this->pendingQueries[$queryKey]);
                        $this->cacheResponse($domain, $type, $response);
                        $this->handleQuerySuccess($response, $server, $remoteAddress, $request, $queryLog, $startTime);
                        $deferred->resolve($response);
                    },
                    function (\Exception $error) use ($server, $remoteAddress, $request, $queryLog, $startTime, $deferred, $queryKey) {
                        unset($this->pendingQueries[$queryKey]);
                        $this->handleQueryFailure($error, $server, $remoteAddress, $request, $queryLog, $startTime);
                        $deferred->reject($error);
                    }
                );

        } catch (\Throwable $e) {
            $this->logger->error('DNS query handling error', [
                'remote_address' => $remoteAddress,
                'domain' => $domain ?? null,
                'type' => $type ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'duration_ms' => (int)((microtime(true) - $startTime) * 1000),
            ]);

            if (isset($queryKey)) {
                unset($this->pendingQueries[$queryKey]);
            }
        }
    }
}
