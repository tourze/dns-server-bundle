<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Integration\Service;

use DnsServerBundle\Entity\DnsQueryLog;
use DnsServerBundle\Entity\UpstreamDnsServer;
use DnsServerBundle\Service\AdminMenu;
use Knp\Menu\ItemInterface;
use PHPUnit\Framework\TestCase;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;

class AdminMenuTest extends TestCase
{
    private AdminMenu $adminMenu;
    private LinkGeneratorInterface $linkGenerator;

    protected function setUp(): void
    {
        $this->linkGenerator = $this->createMock(LinkGeneratorInterface::class);
        $this->adminMenu = new AdminMenu($this->linkGenerator);
    }

    public function testInvokeCreatesMenuStructure(): void
    {
        $rootItem = $this->createMock(ItemInterface::class);
        $dnsMenuItem = $this->createMock(ItemInterface::class);
        $upstreamMenuItem = $this->createMock(ItemInterface::class);
        $queryLogMenuItem = $this->createMock(ItemInterface::class);

        // 模拟 getChild 的两次调用：第一次返回 null，第二次返回 dnsMenuItem
        $rootItem->expects($this->exactly(2))
            ->method('getChild')
            ->with('DNS服务器')
            ->willReturnOnConsecutiveCalls(null, $dnsMenuItem);

        $rootItem->expects($this->once())
            ->method('addChild')
            ->with('DNS服务器')
            ->willReturn($dnsMenuItem);

        // 模拟添加子菜单
        $dnsMenuItem->expects($this->exactly(2))
            ->method('addChild')
            ->willReturnMap([
                ['上游DNS服务器', [], $upstreamMenuItem],
                ['DNS查询日志', [], $queryLogMenuItem],
            ]);

        // 模拟设置URI和属性
        $this->linkGenerator->expects($this->exactly(2))
            ->method('getCurdListPage')
            ->willReturnMap([
                [UpstreamDnsServer::class, '/admin/upstream-dns-server'],
                [DnsQueryLog::class, '/admin/dns-query-log'],
            ]);

        $upstreamMenuItem->expects($this->once())
            ->method('setUri')
            ->with('/admin/upstream-dns-server')
            ->willReturnSelf();

        $upstreamMenuItem->expects($this->once())
            ->method('setAttribute')
            ->with('icon', 'fas fa-server')
            ->willReturnSelf();

        $queryLogMenuItem->expects($this->once())
            ->method('setUri')
            ->with('/admin/dns-query-log')
            ->willReturnSelf();

        $queryLogMenuItem->expects($this->once())
            ->method('setAttribute')
            ->with('icon', 'fas fa-history')
            ->willReturnSelf();

        // 执行测试
        $this->adminMenu->__invoke($rootItem);
    }

    public function testInvokeWithExistingDnsMenu(): void
    {
        $rootItem = $this->createMock(ItemInterface::class);
        $dnsMenuItem = $this->createMock(ItemInterface::class);

        // 模拟 getChild 的两次调用：都返回现有的 dnsMenuItem
        $rootItem->expects($this->exactly(2))
            ->method('getChild')
            ->with('DNS服务器')
            ->willReturn($dnsMenuItem);

        $rootItem->expects($this->never())
            ->method('addChild');

        $dnsMenuItem->expects($this->exactly(2))
            ->method('addChild')
            ->willReturnOnConsecutiveCalls(
                $this->createMock(ItemInterface::class),
                $this->createMock(ItemInterface::class)
            );

        $this->linkGenerator->expects($this->exactly(2))
            ->method('getCurdListPage')
            ->willReturn('/test-uri');

        // 执行测试
        $this->adminMenu->__invoke($rootItem);
    }
}