<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\DependencyInjection;

use DnsServerBundle\DependencyInjection\DnsServerExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DnsServerExtensionTest extends TestCase
{
    private DnsServerExtension $extension;
    private ContainerBuilder $container;

    protected function setUp(): void
    {
        $this->extension = new DnsServerExtension();
        $this->container = new ContainerBuilder();
    }

    public function testLoad(): void
    {
        // 确保services.yaml存在
        $configPath = dirname(__DIR__, 2) . '/src/Resources/config/services.yaml';
        $this->assertFileExists($configPath, 'services.yaml文件必须存在');

        // 加载服务配置
        $this->extension->load([], $this->container);

        // 验证是否定义了dns_server相关服务
        $this->assertTrue(
            $this->container->hasDefinition('DnsServerBundle\Service\DnsMatcherService') || 
            $this->container->hasAlias('DnsServerBundle\Service\DnsMatcherService') ||
            $this->container->has('DnsServerBundle\Service\DnsMatcherService'),
            'DnsMatcherService服务应该被定义'
        );
        
        $this->assertTrue(
            $this->container->hasDefinition('DnsServerBundle\Service\DnsResolver') || 
            $this->container->hasAlias('DnsServerBundle\Service\DnsResolver') ||
            $this->container->has('DnsServerBundle\Service\DnsResolver'),
            'DnsResolver服务应该被定义'
        );
        
        $this->assertTrue(
            $this->container->hasDefinition('DnsServerBundle\Service\DnsWorkerService') || 
            $this->container->hasAlias('DnsServerBundle\Service\DnsWorkerService') ||
            $this->container->has('DnsServerBundle\Service\DnsWorkerService'),
            'DnsWorkerService服务应该被定义'
        );
        
        $this->assertTrue(
            $this->container->hasDefinition('DnsServerBundle\Service\DnsQueryService') || 
            $this->container->hasAlias('DnsServerBundle\Service\DnsQueryService') ||
            $this->container->has('DnsServerBundle\Service\DnsQueryService'),
            'DnsQueryService服务应该被定义'
        );

        // 验证命令是否被注册
        $this->assertTrue(
            $this->container->hasDefinition('DnsServerBundle\Command\DnsWorkerCommand') || 
            $this->container->hasAlias('DnsServerBundle\Command\DnsWorkerCommand') ||
            $this->container->has('DnsServerBundle\Command\DnsWorkerCommand'),
            'DnsWorkerCommand应该被定义'
        );
    }

    public function testLoadWithConfig(): void
    {
        $configs = [
            'dns_server' => [
                'cache_ttl' => 3600,
                'log_enabled' => true,
            ]
        ];

        // 加载服务配置并传入自定义配置
        $this->extension->load($configs, $this->container);

        // 验证服务配置是否生效
        // 此测试只是验证load方法不会因为配置参数而失败
        // 真正的配置处理测试需要在集成测试中完成
        $this->assertTrue(true, '加载带有配置的Extension应该不抛出异常');
    }
} 