<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Service;

use DnsServerBundle\Entity\UpstreamDnsServer;
use DnsServerBundle\Enum\DnsProtocolEnum;
use DnsServerBundle\Service\DnsResolver;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use React\Dns\Model\Message;
use React\Promise\PromiseInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @internal
 */
#[CoversClass(DnsResolver::class)]
final class DnsResolverTest extends TestCase
{
    private DnsResolver $resolver;

    private UpstreamDnsServer $server;

    protected function setUp(): void
    {
        parent::setUp();

        $this->initializeTestObjects();
    }

    private function initializeTestObjects(): void
    {
        $this->resolver = new DnsResolver();

        $this->server = new UpstreamDnsServer();
        $this->server->setHost('8.8.8.8');
        $this->server->setPort(53);
        $this->server->setProtocol(DnsProtocolEnum::UDP);
        $this->server->setTimeout(5);
    }

    public function testCreateCustomResponseWithSingleIpv4(): void
    {
        $this->initializeTestObjects();
        $domain = 'example.com';
        $ips = ['192.168.1.1'];
        $ttl = 300;

        $response = $this->resolver->createCustomResponse($domain, $ips, $ttl);

        $this->assertInstanceOf(Message::class, $response);
        $this->assertTrue($response->qr);
        $this->assertTrue($response->rd);
        $this->assertTrue($response->ra);
        $this->assertSame(Message::RCODE_OK, $response->rcode);

        $this->assertCount(1, $response->questions);
        $this->assertCount(1, $response->answers);

        $question = $response->questions[0];
        $this->assertSame($domain, $question->name);
        $this->assertSame(Message::TYPE_A, $question->type);
        $this->assertSame(Message::CLASS_IN, $question->class);

        $answer = $response->answers[0];
        $this->assertSame($domain, $answer->name);
        $this->assertSame(Message::TYPE_A, $answer->type);
        $this->assertSame(Message::CLASS_IN, $answer->class);
        $this->assertSame($ttl, $answer->ttl);
        $this->assertSame($ips[0], $answer->data);
    }

    public function testCreateCustomResponseWithMultipleIpv4(): void
    {
        $this->initializeTestObjects();
        $domain = 'example.com';
        $ips = ['192.168.1.1', '192.168.1.2', '192.168.1.3'];
        $ttl = 300;

        $response = $this->resolver->createCustomResponse($domain, $ips, $ttl);

        $this->assertInstanceOf(Message::class, $response);
        $this->assertCount(1, $response->questions);
        $this->assertCount(3, $response->answers);

        foreach ($ips as $index => $ip) {
            $answer = $response->answers[$index];
            $this->assertSame($domain, $answer->name);
            $this->assertSame(Message::TYPE_A, $answer->type);
            $this->assertSame($ttl, $answer->ttl);
            $this->assertSame($ip, $answer->data);
        }
    }

    public function testCreateCustomResponseWithIpv6(): void
    {
        $this->initializeTestObjects();
        $domain = 'example.com';
        $ips = ['2001:0db8:85a3:0000:0000:8a2e:0370:7334'];
        $ttl = 300;

        $response = $this->resolver->createCustomResponse($domain, $ips, $ttl, true);

        $this->assertInstanceOf(Message::class, $response);
        $this->assertCount(1, $response->questions);
        $this->assertCount(1, $response->answers);

        $question = $response->questions[0];
        $this->assertSame(Message::TYPE_AAAA, $question->type);

        $answer = $response->answers[0];
        $this->assertSame(Message::TYPE_AAAA, $answer->type);
        $this->assertSame($ips[0], $answer->data);
    }

    public function testCreateCustomResponseWithZeroRecords(): void
    {
        $this->initializeTestObjects();
        $domain = 'example.com';
        $ips = [];
        $ttl = 300;

        $response = $this->resolver->createCustomResponse($domain, $ips, $ttl);

        $this->assertInstanceOf(Message::class, $response);
        $this->assertCount(1, $response->questions);
        $this->assertCount(0, $response->answers);
    }

    public function testQuery(): void
    {
        $this->initializeTestObjects();

        // 验证 query 方法存在并返回 PromiseInterface
        $reflection = new \ReflectionClass($this->resolver);
        $this->assertTrue($reflection->hasMethod('query'));

        $method = $reflection->getMethod('query');
        $this->assertTrue($method->isPublic());

        // 验证参数
        $parameters = $method->getParameters();
        $this->assertCount(2, $parameters);
        $this->assertSame('name', $parameters[0]->getName());
        $this->assertSame('string', (string) $parameters[0]->getType());
        $this->assertSame('server', $parameters[1]->getName());
        $this->assertSame(UpstreamDnsServer::class, (string) $parameters[1]->getType());

        // 验证返回类型
        $returnType = $method->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertSame(PromiseInterface::class, (string) $returnType);
    }

    public function testQueryIpv6(): void
    {
        $this->initializeTestObjects();

        // 验证 queryIpv6 方法存在并返回 PromiseInterface
        $reflection = new \ReflectionClass($this->resolver);
        $this->assertTrue($reflection->hasMethod('queryIpv6'));

        $method = $reflection->getMethod('queryIpv6');
        $this->assertTrue($method->isPublic());

        // 验证参数
        $parameters = $method->getParameters();
        $this->assertCount(2, $parameters);
        $this->assertSame('name', $parameters[0]->getName());
        $this->assertSame('string', (string) $parameters[0]->getType());
        $this->assertSame('server', $parameters[1]->getName());
        $this->assertSame(UpstreamDnsServer::class, (string) $parameters[1]->getType());

        // 验证返回类型
        $returnType = $method->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertSame(PromiseInterface::class, (string) $returnType);
    }
}
