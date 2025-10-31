<?php

declare(strict_types=1);

namespace DnsServerBundle\Service;

use DnsServerBundle\Entity\UpstreamDnsServer;
use DnsServerBundle\Exception\DnsConnectionException;
use DnsServerBundle\Exception\DnsResolutionException;
use React\Dns\Model\Message;
use React\Dns\Model\Record;
use React\Dns\Protocol\BinaryDumper;
use React\Dns\Protocol\Parser;
use React\Dns\Query\Query;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;

/**
 * DNS解析服务
 */
class DnsResolver
{
    private Parser $parser;

    private BinaryDumper $dumper;
    private const MAX_RETRIES = 3;
    private const UDP_MAX_SIZE = 512;
    private const EDNS_UDP_SIZE = 4096;

    public function __construct()
    {
        $this->parser = new Parser();
        $this->dumper = new BinaryDumper();
    }

    /**
     * @return resource
     */
    private function createSocket(string $protocol, string $host, int $port, float $timeout)
    {
        $errno = 0;
        $errstr = '';
        $socket = @stream_socket_client(
            "{$protocol}://{$host}:{$port}",
            $errno,
            $errstr,
            $timeout
        );

        if (false === $socket) {
            $errorMessage = is_string($errstr) ? $errstr : 'Unknown error';
            $errorCode = is_int($errno) ? (string) $errno : 'Unknown code';
            throw new DnsConnectionException('Failed to connect to DNS server: ' . $errorMessage . ' (' . $errorCode . ')');
        }

        if ('tcp' === $protocol) {
            stream_set_blocking($socket, false);
        }

        return $socket;
    }

    private function addEdnsRecord(Message $message): void
    {
        $message->additional[] = new Record(
            '.',
            Message::TYPE_OPT,
            self::EDNS_UDP_SIZE,
            0,
            ''
        );
    }

    /**
     * @return PromiseInterface<Message>
     */
    private function queryWithRetry(string $name, UpstreamDnsServer $server, int $type = Message::TYPE_A, bool $useTcp = false, int $retry = 0): PromiseInterface
    {
        /** @var Deferred<Message> $deferred */
        $deferred = new Deferred();
        $protocol = $useTcp ? 'tcp' : 'udp';

        try {
            $socket = $this->createSocket($protocol, $server->getHost(), $server->getPort(), $server->getTimeout());
            $response = $this->performDnsQuery($socket, $name, $type, $useTcp);

            // 检查是否需要TCP重试
            if (!$useTcp && $response->tc) {
                fclose($socket);

                return $this->queryWithRetry($name, $server, $type, true);
            }

            $deferred->resolve($response);
        } catch (\Throwable $e) {
            if (isset($socket)) {
                fclose($socket);
            }

            return $this->handleQueryError($e, $name, $server, $type, $useTcp, $retry, $deferred);
        }

        return $deferred->promise();
    }

    /**
     * 执行DNS查询并返回响应
     * @param resource $socket
     */
    private function performDnsQuery($socket, string $name, int $type, bool $useTcp): Message
    {
        $query = $this->buildDnsQuery($name, $type);
        $packet = $this->dumper->toBinary($query);

        if ($useTcp) {
            $packet = pack('n', strlen($packet)) . $packet;
        }

        if (false === @stream_socket_sendto($socket, $packet)) {
            throw new DnsResolutionException('Failed to send DNS query');
        }

        return $this->receiveDnsResponse($socket, $useTcp);
    }

    /**
     * 构建DNS查询报文
     */
    private function buildDnsQuery(string $name, int $type): Message
    {
        $query = new Message();
        $query->rd = true; // 递归查询
        $query->questions[] = new Query($name, $type, Message::CLASS_IN);

        // 添加EDNS0支持
        $this->addEdnsRecord($query);

        return $query;
    }

    /**
     * 接收DNS响应
     * @param resource $socket
     */
    private function receiveDnsResponse($socket, bool $useTcp): Message
    {
        if ($useTcp) {
            $buf = $this->receiveTcpResponse($socket);
        } else {
            $buf = @stream_socket_recvfrom($socket, self::UDP_MAX_SIZE);
        }

        if (false === $buf) {
            throw new DnsResolutionException('Failed to receive DNS response');
        }

        return $this->parser->parseMessage($buf);
    }

    /**
     * 接收TCP响应
     * @param resource $socket
     */
    private function receiveTcpResponse($socket): string
    {
        $lengthBin = @stream_get_contents($socket, 2);
        if (false === $lengthBin || 2 !== strlen($lengthBin)) {
            throw new DnsResolutionException('Failed to receive TCP response length');
        }

        $unpackResult = unpack('n', $lengthBin);
        if (false === $unpackResult) {
            throw new DnsResolutionException('Failed to unpack TCP response length');
        }
        $lengthValue = $unpackResult[1];
        assert(is_int($lengthValue));
        $length = $lengthValue;

        $response = @stream_get_contents($socket, $length);
        if (false === $response) {
            throw new DnsResolutionException('Failed to receive TCP response data');
        }

        return $response;
    }

    /**
     * 处理查询错误
     */
    /**
     * @param Deferred<Message> $deferred
     * @return PromiseInterface<Message>
     */
    private function handleQueryError(\Throwable $e, string $name, UpstreamDnsServer $server, int $type, bool $useTcp, int $retry, Deferred $deferred): PromiseInterface
    {
        if ($retry < self::MAX_RETRIES) {
            return $this->queryWithRetry($name, $server, $type, $useTcp, $retry + 1);
        }

        // 如果UDP失败，尝试TCP
        if (!$useTcp) {
            return $this->queryWithRetry($name, $server, $type, true);
        }

        $deferred->reject($e);

        return $deferred->promise();
    }

    /**
     * 查询上游DNS服务器
     */
    /**
     * @return PromiseInterface<Message>
     */
    public function query(string $name, UpstreamDnsServer $server): PromiseInterface
    {
        return $this->queryWithRetry($name, $server);
    }

    /**
     * @return PromiseInterface<Message>
     */
    public function queryIpv6(string $name, UpstreamDnsServer $server): PromiseInterface
    {
        return $this->queryWithRetry($name, $server, Message::TYPE_AAAA);
    }

    /**
     * 使用自定义应答构建DNS响应
     * @param array<string> $ips
     */
    public function createCustomResponse(string $name, array $ips, int $ttl, bool $isIpv6 = false): Message
    {
        $response = new Message();
        $response->qr = true;
        $response->rd = true;
        $response->ra = true;
        $type = $isIpv6 ? Message::TYPE_AAAA : Message::TYPE_A;
        $response->questions[] = new Query($name, $type, Message::CLASS_IN);

        foreach ($ips as $ip) {
            $response->answers[] = new Record(
                $name,
                $type,
                Message::CLASS_IN,
                $ttl,
                $ip
            );
        }

        // 添加EDNS0支持
        $this->addEdnsRecord($response);

        $response->rcode = Message::RCODE_OK;

        return $response;
    }
}
