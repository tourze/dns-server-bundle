<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Service;

use DnsServerBundle\Service\DnsWorkerService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Psr\Log\LoggerInterface;
use React\EventLoop\LoopInterface;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(DnsWorkerService::class)]
#[RunTestsInSeparateProcesses]
final class DnsWorkerServiceTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 不需要设置 LoggerInterface，因为它已经在容器中被初始化了
        // 我们可以直接使用现有的服务
    }

    /**
     * 测试服务依赖项配置
     */
    public function testServiceDependenciesConfiguration(): void
    {
        // 验证必要的依赖项可以从容器获取
        $this->assertTrue(self::getContainer()->has(LoggerInterface::class));
    }

    /**
     * 测试 DnsWorkerService 类的基本结构
     */
    public function testServiceClassStructure(): void
    {
        $reflection = new \ReflectionClass(DnsWorkerService::class);

        // 验证有关键方法
        $this->assertTrue($reflection->hasMethod('start'));

        $startMethod = $reflection->getMethod('start');
        $this->assertTrue($startMethod->isPublic());
    }

    /**
     * 测试 start 方法的参数
     */
    public function testStartMethodParameters(): void
    {
        $reflection = new \ReflectionClass(DnsWorkerService::class);
        $startMethod = $reflection->getMethod('start');

        $parameters = $startMethod->getParameters();
        $this->assertCount(3, $parameters);

        // 第一个参数: loop
        $this->assertSame('loop', $parameters[0]->getName());
        $this->assertSame(LoopInterface::class, (string) $parameters[0]->getType());
        $this->assertFalse($parameters[0]->isDefaultValueAvailable());

        // 第二个参数: host
        $this->assertSame('host', $parameters[1]->getName());
        $this->assertSame('string', (string) $parameters[1]->getType());
        $this->assertTrue($parameters[1]->isDefaultValueAvailable());
        $this->assertSame('0.0.0.0', $parameters[1]->getDefaultValue());

        // 第三个参数: port
        $this->assertSame('port', $parameters[2]->getName());
        $this->assertSame('int', (string) $parameters[2]->getType());
        $this->assertTrue($parameters[2]->isDefaultValueAvailable());
        $this->assertSame(53, $parameters[2]->getDefaultValue());

        // 验证返回类型 (void)
        $returnType = $startMethod->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertSame('void', (string) $returnType);
    }
}
