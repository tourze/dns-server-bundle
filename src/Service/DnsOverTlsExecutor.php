<?php

declare(strict_types=1);

namespace DnsServerBundle\Service;

use DnsServerBundle\Entity\UpstreamDnsServer;
use DnsServerBundle\Exception\QueryFailure;
use React\Dns\Model\Message;
use React\Dns\Model\Record;
use React\Dns\Protocol\BinaryDumper;
use React\Dns\Query\ExecutorInterface;
use React\Dns\Query\Query;
use React\Promise\PromiseInterface;
use function React\Promise\reject;
use function React\Promise\resolve;

class DnsOverTlsExecutor implements ExecutorInterface
{
    public function __construct(
        private readonly UpstreamDnsServer $server,
        private readonly BinaryDumper $dumper,
    ) {
    }

    public function query(Query $query): PromiseInterface
    {
        try {
            $message = new Message();
            $message->qr = false;
            $message->rd = true;
            $message->questions = [
                new Record(
                    $query->name,
                    $query->type,
                    Message::CLASS_IN,
                    0,
                    []
                )
            ];

            $context = stream_context_create([
                'ssl' => [
                    'verify_peer' => $this->server->isVerifyCert(),
                    'verify_peer_name' => $this->server->isVerifyCert(),
                    'local_cert' => $this->server->getCertPath(),
                    'local_pk' => $this->server->getKeyPath(),
                ],
            ]);

            $socket = stream_socket_client(
                'tls://' . $this->server->getHost() . ':' . $this->server->getPort(),
                $errno,
                $errstr,
                $this->server->getTimeout(),
                STREAM_CLIENT_CONNECT,
                $context
            );

            if (!$socket) {
                throw new QueryFailure("Failed to connect to DoT server: $errstr");
            }

            // Send DNS query
            $data = $this->dumper->toBinary($message);
            $length = strlen($data);
            $message = pack('n', $length) . $data;
            fwrite($socket, $message);

            // Read response length
            $lengthBin = fread($socket, 2);
            if (strlen($lengthBin) !== 2) {
                throw new QueryFailure('Failed to read response length');
            }
            $length = unpack('n', $lengthBin)[1];

            // Read response
            $response = fread($socket, $length);
            fclose($socket);

            if (strlen($response) !== $length) {
                throw new QueryFailure('Incomplete response received');
            }

            return resolve($response);
        } catch (\Throwable $e) {
            return reject(new QueryFailure('DoT query failed: ' . $e->getMessage()));
        }
    }
}
