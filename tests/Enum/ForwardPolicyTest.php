<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Enum;

use DnsServerBundle\Enum\ForwardPolicy;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(ForwardPolicy::class)]
final class ForwardPolicyTest extends AbstractEnumTestCase
{
    public function testShouldQueryLocal(): void
    {
        $this->assertTrue(ForwardPolicy::NEVER->shouldQueryLocal());
        $this->assertTrue(ForwardPolicy::FIRST->shouldQueryLocal());
        $this->assertFalse(ForwardPolicy::ONLY->shouldQueryLocal());
        $this->assertTrue(ForwardPolicy::CONDITIONAL->shouldQueryLocal());
    }

    public function testShouldForward(): void
    {
        $this->assertFalse(ForwardPolicy::NEVER->shouldForward());
        $this->assertTrue(ForwardPolicy::FIRST->shouldForward());
        $this->assertTrue(ForwardPolicy::ONLY->shouldForward());
        $this->assertTrue(ForwardPolicy::CONDITIONAL->shouldForward());
    }

    public function testToArray(): void
    {
        // toArray 是实例方法，返回单个枚举项的数组表示
        $array = ForwardPolicy::NEVER->toArray();

        $this->assertIsArray($array);
        $this->assertCount(2, $array);

        // 验证数组的键值对
        $this->assertArrayHasKey('value', $array);
        $this->assertArrayHasKey('label', $array);

        // 验证值是否正确
        $this->assertSame('never', $array['value']);
        $this->assertSame('从不转发', $array['label']);

        // 测试其他枚举值
        $firstArray = ForwardPolicy::FIRST->toArray();
        $this->assertSame('first', $firstArray['value']);
        $this->assertSame('先查本地，找不到再转发', $firstArray['label']);
    }
}
