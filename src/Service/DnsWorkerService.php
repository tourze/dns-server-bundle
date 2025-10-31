<?php

declare(strict_types=1);

namespace DnsServerBundle\Service;

use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use React\Datagram\Factory;
use React\Datagram\Socket;
use React\EventLoop\LoopInterface;
use React\Promise\PromiseInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(public: true)]
#[WithMonologChannel(channel: 'dns_server')]
readonly class DnsWorkerService
{
    public function __construct(
        private DnsQueryService $dnsQueryService,
        private LoggerInterface $logger,
    ) {
    }

    public function start(LoopInterface $loop, string $host = '0.0.0.0', int $port = 53): void
    {
        $factory = new Factory($loop);

        /** @var PromiseInterface<Socket> $promise */
        $promise = $factory->createServer($host . ':' . $port);
        $promise->then(
            function (Socket $server): void {
                $localAddress = $server->getLocalAddress();
                $addressString = is_string($localAddress) ? $localAddress : 'unknown';
                $this->logger->info('DNS Server listening on ' . $addressString);

                $server->on('message', function (string $message, string $remoteAddress, Socket $server): void {
                    $this->handleDnsQuery($message, $remoteAddress, $server);
                });

                $server->on('error', function (\Throwable $error): void {
                    $this->logger->error('DNS Server error: ' . $error->getMessage());
                });
            },
            function (\Throwable $error): void {
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
