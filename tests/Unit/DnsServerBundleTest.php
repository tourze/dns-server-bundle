<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Unit;

use DnsServerBundle\DnsServerBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DnsServerBundleTest extends TestCase
{
    private DnsServerBundle $bundle;

    protected function setUp(): void
    {
        $this->bundle = new DnsServerBundle();
    }

    public function testBundleCanBeInstantiated(): void
    {
        $this->assertInstanceOf(DnsServerBundle::class, $this->bundle);
    }

    public function testBuild(): void
    {
        $container = new ContainerBuilder();
        
        // 测试build方法不抛出异常
        $this->bundle->build($container);
        
        // 这里只是验证方法可以正确调用
        $this->assertTrue(true);
    }
}