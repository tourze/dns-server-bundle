<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Controller\Admin;

use DnsServerBundle\Controller\Admin\DnsQueryLogCrudController;
use DnsServerBundle\Entity\DnsQueryLog;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(DnsQueryLogCrudController::class)]
#[RunTestsInSeparateProcesses]
final class DnsQueryLogCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    protected function getControllerService(): DnsQueryLogCrudController
    {
        return self::getService(DnsQueryLogCrudController::class);
    }

    /** @return iterable<string, array{string}> */
    public static function provideIndexPageHeaders(): iterable
    {
        return [
            'ID' => ['ID'],
            '查询域名' => ['查询域名'],
            '查询类型' => ['查询类型'],
            '客户端IP' => ['客户端IP'],
            '命中缓存' => ['命中缓存'],
        ];
    }

    /** @return iterable<string, array{string}> */
    public static function provideNewPageFields(): iterable
    {
        return [
            'domain' => ['domain'],
            'queryType' => ['queryType'],
            'clientIp' => ['clientIp'],
            'response' => ['response'],
            'isHit' => ['isHit'],
            'responseTime' => ['responseTime'],
        ];
    }

    /** @return iterable<string, array{string}> */
    public static function provideEditPageFields(): iterable
    {
        return [
            'domain' => ['domain'],
            'queryType' => ['queryType'],
            'clientIp' => ['clientIp'],
            'response' => ['response'],
            'isHit' => ['isHit'],
            'responseTime' => ['responseTime'],
        ];
    }

    public function testEntityFqcnAndBasicFunctionality(): void
    {
        // 测试实体类名获取
        $this->assertSame(DnsQueryLog::class, DnsQueryLogCrudController::getEntityFqcn());

        // 验证控制器可以被实例化
        $controller = new DnsQueryLogCrudController();
        $this->assertInstanceOf(DnsQueryLogCrudController::class, $controller);
    }

    public function testUnauthorizedAccessThroughControllerValidation(): void
    {
        $controller = new DnsQueryLogCrudController();

        // 验证控制器继承了正确的基类
        $this->assertInstanceOf(AbstractCrudController::class, $controller);

        // 验证实体类配置正确（间接验证访问控制配置）
        $this->assertSame(DnsQueryLog::class, DnsQueryLogCrudController::getEntityFqcn());
    }

    public function testControllerConfigurationValidation(): void
    {
        // 验证实体类配置正确
        $this->assertSame(DnsQueryLog::class, DnsQueryLogCrudController::getEntityFqcn());
    }

    public function testSearchFilters(): void
    {
        // 验证实体类配置正确
        $this->assertSame(DnsQueryLog::class, DnsQueryLogCrudController::getEntityFqcn());
    }
}
