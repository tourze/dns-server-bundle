<?php

declare(strict_types=1);

namespace DnsServerBundle\Service;

use DnsServerBundle\Entity\UpstreamDnsServer;
use DnsServerBundle\Exception\GeneralQueryFailureException;
use React\Dns\Model\Message;
use React\Dns\Protocol\BinaryDumper;
use React\Dns\Protocol\Parser;
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
        private readonly Parser $parser,
    ) {
    }

    public function query(Query $query): PromiseInterface
    {
        try {
            $message = new Message();
            $message->qr = false;
            $message->rd = true;
            $message->questions = [$query];

            $context = stream_context_create([
                'ssl' => [
                    'verify_peer' => $this->server->isVerifyCert(),
                    'verify_peer_name' => $this->server->isVerifyCert(),
                    'local_cert' => $this->server->getCertPath(),
                    'local_pk' => $this->server->getKeyPath(),
                ],
            ]);

            $errno = 0;
            $errstr = '';
            $socket = @stream_socket_client(
                'tls://' . $this->server->getHost() . ':' . $this->server->getPort(),
                $errno,
                $errstr,
                $this->server->getTimeout(),
                STREAM_CLIENT_CONNECT,
                $context
            );

            if (false === $socket) {
                $errorMessage = is_string($errstr) ? $errstr : 'Unknown error';
                throw new GeneralQueryFailureException('Failed to connect to DoT server: ' . $errorMessage);
            }

            // Send DNS query
            $data = $this->dumper->toBinary($message);
            $length = strlen($data);
            $message = pack('n', $length) . $data;
            fwrite($socket, $message);

            // Read response length
            $lengthBin = fread($socket, 2);
            if (false === $lengthBin || 2 !== strlen($lengthBin)) {
                throw new GeneralQueryFailureException('Failed to read response length');
            }
            $unpackResult = unpack('n', $lengthBin);
            if (false === $unpackResult) {
                throw new GeneralQueryFailureException('Failed to unpack response length');
            }
            $lengthValue = $unpackResult[1];
            assert(is_int($lengthValue));
            $length = max(1, $lengthValue);

            // Read response
            $response = fread($socket, $length);
            fclose($socket);

            if (false === $response || strlen($response) !== $length) {
                throw new GeneralQueryFailureException('Incomplete response received');
            }

            $responseMessage = $this->parser->parseMessage($response);

            return resolve($responseMessage);
        } catch (\Throwable $e) {
            return reject(new GeneralQueryFailureException('DoT query failed: ' . $e->getMessage()));
        }
    }
}
