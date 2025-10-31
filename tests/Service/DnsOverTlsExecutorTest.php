<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Service;

use DnsServerBundle\Entity\UpstreamDnsServer;
use DnsServerBundle\Enum\DnsProtocolEnum;
use DnsServerBundle\Exception\QueryFailureException;
use DnsServerBundle\Service\DnsOverTlsExecutor;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use React\Dns\Model\Message;
use React\Dns\Protocol\BinaryDumper;
use React\Dns\Protocol\Parser;
use React\Dns\Query\ExecutorInterface;
use React\Dns\Query\Query;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @internal
 */
#[CoversClass(DnsOverTlsExecutor::class)]
final class DnsOverTlsExecutorTest extends TestCase
{
    private DnsOverTlsExecutor $executor;

    private UpstreamDnsServer $server;

    protected function setUp(): void
    {
        parent::setUp();

        $this->initializeTestObjects();
    }

    private function initializeTestObjects(): void
    {
        $this->server = new UpstreamDnsServer();
        $this->server->setHost('1.1.1.1');
        $this->server->setPort(853);
        $this->server->setProtocol(DnsProtocolEnum::DOT);
        $this->server->setTimeout(1);
        $this->server->setVerifyCert(false);

        $this->executor = new DnsOverTlsExecutor(
            $this->server,
            new BinaryDumper(),
            new Parser()
        );
    }

    public function testExecutorImplementsInterface(): void
    {
        $this->initializeTestObjects();
        $this->assertInstanceOf(ExecutorInterface::class, $this->executor);
    }

    public function testQueryWithConnectionFailure(): void
    {
        $this->initializeTestObjects();
        // 使用无效的主机来模拟连接失败
        $invalidServer = new UpstreamDnsServer();
        $invalidServer->setHost('invalid.nonexistent.host');
        $invalidServer->setPort(853);
        $invalidServer->setProtocol(DnsProtocolEnum::DOT);
        $invalidServer->setTimeout(1);
        $invalidServer->setVerifyCert(false);

        $executor = new DnsOverTlsExecutor(
            $invalidServer,
            new BinaryDumper(),
            new Parser()
        );

        $query = new Query('example.com', Message::TYPE_A, Message::CLASS_IN);

        // 抑制连接失败的警告
        $originalErrorReporting = error_reporting(E_ALL & ~E_WARNING);
        try {
            $promise = $executor->query($query);
        } finally {
            error_reporting($originalErrorReporting);
        }

        $hasError = false;
        $promise->then(
            function (): void {
                static::fail('Promise should not resolve successfully');
            },
            function (\Throwable $error) use (&$hasError): void {
                $hasError = true;
                $this->assertInstanceOf(QueryFailureException::class, $error);
                $this->assertStringContainsString('DoT query failed', $error->getMessage());
            }
        );

        $this->assertTrue($hasError, 'Query should trigger error callback');
    }

    public function testQueryWithInvalidPort(): void
    {
        $this->initializeTestObjects();
        // 使用无效端口来模拟连接失败
        $invalidServer = new UpstreamDnsServer();
        $invalidServer->setHost('1.1.1.1');
        $invalidServer->setPort(99999); // 无效端口
        $invalidServer->setProtocol(DnsProtocolEnum::DOT);
        $invalidServer->setTimeout(1);
        $invalidServer->setVerifyCert(false);

        $executor = new DnsOverTlsExecutor(
            $invalidServer,
            new BinaryDumper(),
            new Parser()
        );

        $query = new Query('example.com', Message::TYPE_A, Message::CLASS_IN);

        // 抑制连接失败的警告
        $originalErrorReporting = error_reporting(E_ALL & ~E_WARNING);
        try {
            $promise = $executor->query($query);
        } finally {
            error_reporting($originalErrorReporting);
        }

        $hasError = false;
        $promise->then(
            function (): void {
                static::fail('Promise should not resolve successfully');
            },
            function (\Throwable $error) use (&$hasError): void {
                $hasError = true;
                $this->assertInstanceOf(QueryFailureException::class, $error);
            }
        );

        $this->assertTrue($hasError, 'Query should trigger error callback');
    }
}
