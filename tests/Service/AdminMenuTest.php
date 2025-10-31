<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Service;

use DnsServerBundle\Service\AdminMenu;
use Knp\Menu\ItemInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\MockObject\MockObject;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;

/**
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
final class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    private AdminMenu $adminMenu;

    private LinkGeneratorInterface&MockObject $linkGenerator;

    protected function onSetUp(): void
    {
        // 创建模拟服务
        $this->linkGenerator = $this->createMock(LinkGeneratorInterface::class);
        $this->linkGenerator->expects($this->any())
            ->method('getCurdListPage')
            ->willReturn('/admin/dns')
        ;

        // 设置到容器中
        self::getContainer()->set(LinkGeneratorInterface::class, $this->linkGenerator);

        // 从容器获取服务
        $this->adminMenu = self::getService(AdminMenu::class);
    }

    public function testInvokeShouldBeCallable(): void
    {
        // AdminMenu实现了__invoke方法，所以是可调用的
        $reflection = new \ReflectionClass(AdminMenu::class);
        $this->assertTrue($reflection->hasMethod('__invoke'));
    }

    public function testInvokeWithBasicItemShouldNotThrowException(): void
    {
        $item = $this->createMock(ItemInterface::class);
        $dnsMenuItem = $this->createMock(ItemInterface::class);

        // 为链式调用创建子菜单项mock
        $upstreamServerMenuItem = $this->createMock(ItemInterface::class);
        $queryLogMenuItem = $this->createMock(ItemInterface::class);

        // 设置 getChild 返回 null (第一次) 和 dnsMenuItem (第二次)
        $item->expects($this->exactly(2))
            ->method('getChild')
            ->with('DNS服务器')
            ->willReturnOnConsecutiveCalls(null, $dnsMenuItem)
        ;

        // 设置 addChild 返回 dnsMenuItem
        $item->expects($this->once())
            ->method('addChild')
            ->with('DNS服务器')
            ->willReturn($dnsMenuItem)
        ;

        // 设置 dnsMenuItem 的 addChild 调用
        $dnsMenuItem->expects($this->exactly(2))
            ->method('addChild')
            ->willReturnCallback(function (string $name) use ($upstreamServerMenuItem, $queryLogMenuItem): ItemInterface {
                return '上游DNS服务器' === $name ? $upstreamServerMenuItem : $queryLogMenuItem;
            })
        ;

        // 设置链式调用 - 上游DNS服务器菜单项
        $upstreamServerMenuItem->expects($this->once())
            ->method('setUri')
            ->willReturnSelf()
        ;
        $upstreamServerMenuItem->expects($this->once())
            ->method('setAttribute')
            ->with('icon', 'fas fa-server')
            ->willReturnSelf()
        ;

        // 设置链式调用 - DNS查询日志菜单项
        $queryLogMenuItem->expects($this->once())
            ->method('setUri')
            ->willReturnSelf()
        ;
        $queryLogMenuItem->expects($this->once())
            ->method('setAttribute')
            ->with('icon', 'fas fa-history')
            ->willReturnSelf()
        ;

        try {
            ($this->adminMenu)($item);
            // 测试成功通过，无需断言
        } catch (\Throwable $e) {
            self::fail('调用AdminMenu不应抛出异常: ' . $e->getMessage());
        }
    }
}
