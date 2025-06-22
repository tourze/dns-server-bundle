<?php

namespace DnsServerBundle\Service;

use DnsServerBundle\Entity\UpstreamDnsServer;
use React\Dns\Model\Message;
use React\Dns\Model\Record;
use React\Dns\Protocol\BinaryDumper;
use React\Dns\Protocol\Parser;
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

    private function createSocket(string $protocol, string $host, int $port, float $timeout): mixed
    {
        $errno = 0;
        $errstr = '';
        $socket = @stream_socket_client(
            "$protocol://$host:$port",
            $errno,
            $errstr,
            $timeout
        );

        if (!$socket) {
            throw new \RuntimeException("Failed to connect to DNS server: $errstr ($errno)");
        }

        if ($protocol === 'tcp') {
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

    private function queryWithRetry(string $name, UpstreamDnsServer $server, int $type = Message::TYPE_A, bool $useTcp = false, int $retry = 0): PromiseInterface
    {
        $deferred = new Deferred();
        $protocol = $useTcp ? 'tcp' : 'udp';

        try {
            $socket = $this->createSocket($protocol, $server->getHost(), $server->getPort(), $server->getTimeout());

            // 构建查询报文
            $query = new Message();
            $query->rd = true; // 递归查询
            $query->questions[] = new Record($name, $type, Message::CLASS_IN, 0, '');

            // 添加EDNS0支持
            $this->addEdnsRecord($query);

            // 发送查询
            $packet = $this->dumper->toBinary($query);

            if ($useTcp) {
                $packet = pack('n', strlen($packet)) . $packet;
            }

            if (@stream_socket_sendto($socket, $packet) === false) {
                throw new \RuntimeException('Failed to send DNS query');
            }

            // 接收响应
            if ($useTcp) {
                $lengthBin = @stream_get_contents($socket, 2);
                if ($lengthBin === false || strlen($lengthBin) !== 2) {
                    throw new \RuntimeException('Failed to receive TCP response length');
                }
                $length = unpack('n', $lengthBin)[1];
                $buf = @stream_get_contents($socket, $length);
            } else {
                $buf = @stream_socket_recvfrom($socket, self::UDP_MAX_SIZE);
            }

            if ($buf === false) {
                throw new \RuntimeException('Failed to receive DNS response');
            }

            $response = $this->parser->parseMessage($buf);

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

            if ($retry < self::MAX_RETRIES) {
                // 重试逻辑
                return $this->queryWithRetry($name, $server, $type, $useTcp, $retry + 1);
            }

            // 如果UDP失败，尝试TCP
            if (!$useTcp) {
                return $this->queryWithRetry($name, $server, $type, true);
            }

            $deferred->reject($e);
        }

        return $deferred->promise();
    }

    /**
     * 查询上游DNS服务器
     */
    public function query(string $name, UpstreamDnsServer $server): PromiseInterface
    {
        return $this->queryWithRetry($name, $server);
    }

    public function queryIpv6(string $name, UpstreamDnsServer $server): PromiseInterface
    {
        return $this->queryWithRetry($name, $server, Message::TYPE_AAAA);
    }

    /**
     * 使用自定义应答构建DNS响应
     */
    public function createCustomResponse(string $name, array $ips, int $ttl, bool $isIpv6 = false): Message
    {
        $response = new Message();
        $response->qr = true;
        $response->rd = true;
        $response->ra = true;
        $type = $isIpv6 ? Message::TYPE_AAAA : Message::TYPE_A;
        $response->questions[] = new Record($name, $type, Message::CLASS_IN, 0, '');

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
