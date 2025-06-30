<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Integration\Service;

use DnsServerBundle\Entity\UpstreamDnsServer;
use DnsServerBundle\Enum\DnsProtocolEnum;
use DnsServerBundle\Service\DnsOverTlsExecutor;
use PHPUnit\Framework\TestCase;
use React\Dns\Model\Message;
use React\Dns\Protocol\BinaryDumper;
use React\Dns\Protocol\Parser;
use React\Dns\Query\Query;

class DnsOverTlsExecutorTest extends TestCase
{
    private DnsOverTlsExecutor $executor;
    private UpstreamDnsServer $server;

    protected function setUp(): void
    {
        $this->server = new UpstreamDnsServer();
        $this->server->setHost('1.1.1.1')
            ->setPort(853)
            ->setProtocol(DnsProtocolEnum::DOT)
            ->setTimeout(1)
            ->setVerifyCert(false);
        
        $this->executor = new DnsOverTlsExecutor(
            $this->server,
            new BinaryDumper(),
            new Parser()
        );
    }

    public function testExecutorImplementsInterface(): void
    {
        $this->assertInstanceOf(\React\Dns\Query\ExecutorInterface::class, $this->executor);
    }

    public function testQueryWithConnectionFailure(): void
    {
        // 使用无效的主机来模拟连接失败
        $invalidServer = new UpstreamDnsServer();
        $invalidServer->setHost('invalid.nonexistent.host')
            ->setPort(853)
            ->setProtocol(DnsProtocolEnum::DOT)
            ->setTimeout(1)
            ->setVerifyCert(false);
        
        $executor = new DnsOverTlsExecutor(
            $invalidServer,
            new BinaryDumper(),
            new Parser()
        );
        
        $query = new Query('example.com', Message::TYPE_A, Message::CLASS_IN);
        $promise = $executor->query($query);
        
        $hasError = false;
        $promise->then(
            function () {
                $this->fail('Promise should not resolve successfully');
            },
            function (\Throwable $error) use (&$hasError) {
                $hasError = true;
                $this->assertInstanceOf(\DnsServerBundle\Exception\QueryFailure::class, $error);
                $this->assertStringContainsString('DoT query failed', $error->getMessage());
            }
        );
        
        $this->assertTrue($hasError, 'Query should trigger error callback');
    }

    public function testQueryWithInvalidPort(): void
    {
        // 使用无效端口来模拟连接失败
        $invalidServer = new UpstreamDnsServer();
        $invalidServer->setHost('1.1.1.1')
            ->setPort(99999) // 无效端口
            ->setProtocol(DnsProtocolEnum::DOT)
            ->setTimeout(1)
            ->setVerifyCert(false);
        
        $executor = new DnsOverTlsExecutor(
            $invalidServer,
            new BinaryDumper(),
            new Parser()
        );
        
        $query = new Query('example.com', Message::TYPE_A, Message::CLASS_IN);
        $promise = $executor->query($query);
        
        $hasError = false;
        $promise->then(
            function () {
                $this->fail('Promise should not resolve successfully');
            },
            function (\Throwable $error) use (&$hasError) {
                $hasError = true;
                $this->assertInstanceOf(\DnsServerBundle\Exception\QueryFailure::class, $error);
            }
        );
        
        $this->assertTrue($hasError, 'Query should trigger error callback');
    }
}