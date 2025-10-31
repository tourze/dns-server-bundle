<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Service;

use DnsServerBundle\Service\DnsQueryService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(DnsQueryService::class)]
#[RunTestsInSeparateProcesses]
final class DnsQueryServiceTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 设置测试所需的基本服务
        $httpClient = $this->createMock(HttpClientInterface::class);
        $cache = $this->createMock(AdapterInterface::class);

        self::getContainer()->set(HttpClientInterface::class, $httpClient);
        self::getContainer()->set(AdapterInterface::class, $cache);
    }

    /**
     * 测试服务依赖项配置
     */
    public function testServiceDependenciesConfiguration(): void
    {
        // 验证必要的依赖项可以从容器获取
        $this->assertTrue(self::getContainer()->has(HttpClientInterface::class));
        $this->assertTrue(self::getContainer()->has(AdapterInterface::class));
    }

    /**
     * 测试 DnsQueryService 类的基本结构
     */
    public function testServiceClassStructure(): void
    {
        $reflection = new \ReflectionClass(DnsQueryService::class);

        // 验证有关键方法
        $this->assertTrue($reflection->hasMethod('handleQuery'));

        $handleQueryMethod = $reflection->getMethod('handleQuery');
        $this->assertTrue($handleQueryMethod->isPublic());
    }

    /**
     * 测试构造函数参数
     */
    public function testConstructorParameters(): void
    {
        $reflection = new \ReflectionClass(DnsQueryService::class);
        $constructor = $reflection->getConstructor();

        $this->assertNotNull($constructor);

        $parameters = $constructor->getParameters();
        $this->assertCount(5, $parameters);

        // 验证 HttpClientInterface 参数
        $httpClientParam = null;
        foreach ($parameters as $param) {
            if ('httpClient' === $param->getName()) {
                $httpClientParam = $param;
                break;
            }
        }

        $this->assertNotNull($httpClientParam);
        $this->assertEquals(HttpClientInterface::class, (string) $httpClientParam->getType());
    }

    /**
     * 测试 handleQuery 方法存在且具有正确的签名
     */
    public function testHandleQuery(): void
    {
        $reflection = new \ReflectionClass(DnsQueryService::class);

        // 验证 handleQuery 方法存在
        $this->assertTrue($reflection->hasMethod('handleQuery'));

        $handleQueryMethod = $reflection->getMethod('handleQuery');

        // 验证方法是公共的
        $this->assertTrue($handleQueryMethod->isPublic());

        // 验证方法参数
        $parameters = $handleQueryMethod->getParameters();
        $this->assertCount(3, $parameters);

        // 验证参数类型和名称
        $this->assertEquals('message', $parameters[0]->getName());
        $this->assertEquals('string', (string) $parameters[0]->getType());

        $this->assertEquals('remoteAddress', $parameters[1]->getName());
        $this->assertEquals('string', (string) $parameters[1]->getType());

        $this->assertEquals('server', $parameters[2]->getName());
        $this->assertEquals('React\Datagram\Socket', (string) $parameters[2]->getType());

        // 验证返回类型
        $returnType = $handleQueryMethod->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('void', (string) $returnType);
    }
}
