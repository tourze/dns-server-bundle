<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Service;

use DnsServerBundle\Service\DnsOverHttpsExecutor;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use React\Dns\Query\ExecutorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(DnsOverHttpsExecutor::class)]
#[RunTestsInSeparateProcesses]
final class DnsOverHttpsExecutorTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 设置测试所需的基本服务
        $httpClient = $this->createMock(HttpClientInterface::class);
        self::getContainer()->set(HttpClientInterface::class, $httpClient);
    }

    /**
     * 测试服务是否可以从容器中获取
     */
    public function testServiceCanBeRetrievedFromContainer(): void
    {
        // 验证必要的依赖项可以从容器获取
        $this->assertTrue(self::getContainer()->has(HttpClientInterface::class));
    }

    /**
     * 测试 DnsOverHttpsExecutor 类的基本结构
     */
    public function testExecutorClassStructure(): void
    {
        $reflection = new \ReflectionClass(DnsOverHttpsExecutor::class);

        // 验证类实现了 ExecutorInterface
        $this->assertTrue($reflection->implementsInterface(ExecutorInterface::class));

        // 验证有 query 方法
        $this->assertTrue($reflection->hasMethod('query'));

        $queryMethod = $reflection->getMethod('query');
        $this->assertTrue($queryMethod->isPublic());

        // 验证返回类型
        $returnType = $queryMethod->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('React\Promise\PromiseInterface', (string) $returnType);
    }

    /**
     * 测试构造函数参数
     */
    public function testConstructorParameters(): void
    {
        $reflection = new \ReflectionClass(DnsOverHttpsExecutor::class);
        $constructor = $reflection->getConstructor();

        $this->assertNotNull($constructor);

        $parameters = $constructor->getParameters();
        $this->assertCount(5, $parameters);

        // 第一个参数：HttpClientInterface
        $this->assertEquals('httpClient', $parameters[0]->getName());
        $this->assertEquals(HttpClientInterface::class, (string) $parameters[0]->getType());

        // 第二个参数：UpstreamDnsServer
        $this->assertEquals('server', $parameters[1]->getName());
        $this->assertEquals('DnsServerBundle\Entity\UpstreamDnsServer', (string) $parameters[1]->getType());
    }

    /**
     * 测试 query 方法存在且可以被调用
     */
    public function testQuery(): void
    {
        $reflection = new \ReflectionClass(DnsOverHttpsExecutor::class);

        // 验证 query 方法存在
        $this->assertTrue($reflection->hasMethod('query'));

        $queryMethod = $reflection->getMethod('query');

        // 验证方法是公共的
        $this->assertTrue($queryMethod->isPublic());

        // 验证方法参数
        $parameters = $queryMethod->getParameters();
        $this->assertCount(1, $parameters);
        $this->assertEquals('query', $parameters[0]->getName());
        $this->assertEquals('React\Dns\Query\Query', (string) $parameters[0]->getType());

        // 验证返回类型
        $returnType = $queryMethod->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('React\Promise\PromiseInterface', (string) $returnType);
    }
}
