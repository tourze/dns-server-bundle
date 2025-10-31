<?php

declare(strict_types=1);

namespace DnsServerBundle\Service;

use DnsServerBundle\Entity\UpstreamDnsServer;
use DnsServerBundle\Exception\GeneralQueryFailureException;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use React\Dns\Model\Message;
use React\Dns\Protocol\BinaryDumper;
use React\Dns\Protocol\Parser;
use React\Dns\Query\ExecutorInterface;
use React\Dns\Query\Query;
use React\Promise\PromiseInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use function React\Promise\reject;
use function React\Promise\resolve;

#[WithMonologChannel(channel: 'dns_server')]
class DnsOverHttpsExecutor implements ExecutorInterface
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly UpstreamDnsServer $server,
        private readonly BinaryDumper $dumper,
        private readonly Parser $parser,
        private readonly LoggerInterface $logger = new NullLogger(),
    ) {
    }

    public function query(Query $query): PromiseInterface
    {
        $startTime = microtime(true);
        $queryName = $query->name;
        $queryType = $query->type;

        $this->logger->info('DNS over HTTPS query started', [
            'server' => $this->server->getHost(),
            'query_name' => $queryName,
            'query_type' => $queryType,
        ]);

        try {
            $message = new Message();
            $message->qr = false;
            $message->rd = true;
            $message->questions = [$query];

            $url = 'https://' . $this->server->getHost() . '/dns-query';

            $options = [
                'headers' => [
                    'Accept' => 'application/dns-message',
                    'Content-Type' => 'application/dns-message',
                ],
                'body' => $this->dumper->toBinary($message),
                'timeout' => $this->server->getTimeout(),
                'verify_peer' => $this->server->isVerifyCert(),
                'verify_host' => $this->server->isVerifyCert(),
            ];

            $response = $this->httpClient->request('POST', $url, $options);

            if (200 !== $response->getStatusCode()) {
                $error = new GeneralQueryFailureException('DoH query failed: HTTP status ' . $response->getStatusCode());
                $this->logQueryResult($queryName, $queryType, $startTime, false, $error->getMessage());

                return reject($error);
            }

            $responseData = $response->getContent();
            $responseMessage = $this->parser->parseMessage($responseData);

            $this->logQueryResult($queryName, $queryType, $startTime, true, 'Success', $response->getStatusCode(), strlen($responseData));

            return resolve($responseMessage);
        } catch (\Throwable $e) {
            $error = new GeneralQueryFailureException('DoH query failed: ' . $e->getMessage());
            $this->logQueryResult($queryName, $queryType, $startTime, false, $e->getMessage());

            return reject($error);
        }
    }

    /**
     * 记录查询结果审计日志
     */
    private function logQueryResult(
        string $queryName,
        int $queryType,
        float $startTime,
        bool $success,
        string $message,
        ?int $httpStatus = null,
        ?int $responseSize = null,
    ): void {
        $duration = (microtime(true) - $startTime) * 1000; // 转换为毫秒

        $context = [
            'server' => $this->server->getHost(),
            'query_name' => $queryName,
            'query_type' => $queryType,
            'duration_ms' => round($duration, 2),
            'success' => $success,
            'message' => $message,
        ];

        if (null !== $httpStatus) {
            $context['http_status'] = $httpStatus;
        }

        if (null !== $responseSize) {
            $context['response_size_bytes'] = $responseSize;
        }

        if ($success) {
            $this->logger->info('DNS over HTTPS query completed successfully', $context);
        } else {
            $this->logger->error('DNS over HTTPS query failed', $context);
        }
    }
}
