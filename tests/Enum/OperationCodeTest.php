<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Enum;

use DnsServerBundle\Enum\OperationCode;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(OperationCode::class)]
final class OperationCodeTest extends AbstractEnumTestCase
{
    public function testEnumValues(): void
    {
        $this->assertSame(0, OperationCode::QUERY->value);
        $this->assertSame(1, OperationCode::IQUERY->value);
        $this->assertSame(2, OperationCode::STATUS->value);
        $this->assertSame(4, OperationCode::NOTIFY->value);
        $this->assertSame(5, OperationCode::UPDATE->value);
    }

    public function testGetDescription(): void
    {
        $this->assertSame('标准查询', OperationCode::QUERY->getDescription());
        $this->assertSame('反向查询', OperationCode::IQUERY->getDescription());
        $this->assertSame('服务器状态请求', OperationCode::STATUS->getDescription());
        $this->assertSame('通知', OperationCode::NOTIFY->getDescription());
        $this->assertSame('动态更新', OperationCode::UPDATE->getDescription());
    }

    public function testGetLabel(): void
    {
        $this->assertSame('标准查询', OperationCode::QUERY->getLabel());
        $this->assertSame('反向查询', OperationCode::IQUERY->getLabel());
        $this->assertSame('服务器状态请求', OperationCode::STATUS->getLabel());
        $this->assertSame('通知', OperationCode::NOTIFY->getLabel());
        $this->assertSame('动态更新', OperationCode::UPDATE->getLabel());
    }

    public function testToArray(): void
    {
        // toArray 是实例方法，返回单个枚举项的数组表示
        $array = OperationCode::QUERY->toArray();

        $this->assertIsArray($array);
        $this->assertCount(2, $array);

        // 验证数组的键值对
        $this->assertArrayHasKey('value', $array);
        $this->assertArrayHasKey('label', $array);

        // 验证值是否正确
        $this->assertSame(0, $array['value']);
        $this->assertSame('标准查询', $array['label']);

        // 测试其他枚举值
        $updateArray = OperationCode::UPDATE->toArray();
        $this->assertSame(5, $updateArray['value']);
        $this->assertSame('动态更新', $updateArray['label']);
    }
}
