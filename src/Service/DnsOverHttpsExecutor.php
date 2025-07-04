<?php

declare(strict_types=1);

namespace DnsServerBundle\Service;

use DnsServerBundle\Entity\UpstreamDnsServer;
use DnsServerBundle\Exception\QueryFailure;
use React\Dns\Model\Message;
use React\Dns\Protocol\BinaryDumper;
use React\Dns\Protocol\Parser;
use React\Dns\Query\ExecutorInterface;
use React\Dns\Query\Query;
use React\Promise\PromiseInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use function React\Promise\reject;
use function React\Promise\resolve;

class DnsOverHttpsExecutor implements ExecutorInterface
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
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
            
            if ($response->getStatusCode() !== 200) {
                return reject(new QueryFailure('DoH query failed: HTTP status ' . $response->getStatusCode()));
            }
            
            $responseData = $response->getContent();
            $responseMessage = $this->parser->parseMessage($responseData);
            
            return resolve($responseMessage);
        } catch (\Throwable $e) {
            return reject(new QueryFailure('DoH query failed: ' . $e->getMessage()));
        }
    }
}
