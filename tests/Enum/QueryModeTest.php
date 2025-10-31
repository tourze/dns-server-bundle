<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Enum;

use DnsServerBundle\Enum\QueryMode;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(QueryMode::class)]
final class QueryModeTest extends AbstractEnumTestCase
{
    public function testEnumValues(): void
    {
        $this->assertSame('recursive', QueryMode::RECURSIVE->value);
        $this->assertSame('iterative', QueryMode::ITERATIVE->value);
    }

    public function testGetDescription(): void
    {
        $this->assertSame('递归查询', QueryMode::RECURSIVE->getDescription());
        $this->assertSame('迭代查询', QueryMode::ITERATIVE->getDescription());
    }

    public function testGetLabel(): void
    {
        $this->assertSame('递归查询', QueryMode::RECURSIVE->getLabel());
        $this->assertSame('迭代查询', QueryMode::ITERATIVE->getLabel());
    }

    public function testIsRecursive(): void
    {
        $this->assertTrue(QueryMode::RECURSIVE->isRecursive());
        $this->assertFalse(QueryMode::ITERATIVE->isRecursive());
    }

    public function testIsIterative(): void
    {
        $this->assertFalse(QueryMode::RECURSIVE->isIterative());
        $this->assertTrue(QueryMode::ITERATIVE->isIterative());
    }

    public function testToArray(): void
    {
        // toArray 是实例方法，返回单个枚举项的数组表示
        $array = QueryMode::RECURSIVE->toArray();

        $this->assertIsArray($array);
        $this->assertCount(2, $array);

        // 验证数组的键值对
        $this->assertArrayHasKey('value', $array);
        $this->assertArrayHasKey('label', $array);

        // 验证值是否正确
        $this->assertSame('recursive', $array['value']);
        $this->assertSame('递归查询', $array['label']);

        // 测试其他枚举值
        $iterativeArray = QueryMode::ITERATIVE->toArray();
        $this->assertSame('iterative', $iterativeArray['value']);
        $this->assertSame('迭代查询', $iterativeArray['label']);
    }
}
