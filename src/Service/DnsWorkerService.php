<?php

declare(strict_types=1);

namespace DnsServerBundle\Service;

use Psr\Log\LoggerInterface;
use React\Datagram\Factory;
use React\Datagram\Socket;
use React\EventLoop\LoopInterface;

class DnsWorkerService
{
    public function __construct(
        private readonly DnsQueryService $dnsQueryService,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function start(LoopInterface $loop, string $host = '0.0.0.0', int $port = 53): void
    {
        $factory = new Factory($loop);

        $factory->createServer($host . ':' . $port)->then(
            function (Socket $server) {
                $this->logger->info('DNS Server listening on ' . $server->getLocalAddress());

                $server->on('message', function ($message, $remoteAddress, $server) {
                    $this->handleDnsQuery($message, $remoteAddress, $server);
                });

                $server->on('error', function ($error) {
                    $this->logger->error('DNS Server error: ' . $error->getMessage());
                });
            },
            function ($error) {
                $this->logger->error('DNS Server failed to start: ' . $error->getMessage());
            }
        );
    }

    private function handleDnsQuery(string $message, string $remoteAddress, Socket $server): void
    {
        try {
            $this->dnsQueryService->handleQuery($message, $remoteAddress, $server);
        } catch (\Throwable $e) {
            $this->logger->error('DNS query handling error: ' . $e->getMessage(), [
                'remote_address' => $remoteAddress,
                'exception' => $e,
            ]);
        }
    }
}
