<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Enum;

use DnsServerBundle\Enum\CacheStrategy;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(CacheStrategy::class)]
final class CacheStrategyTest extends AbstractEnumTestCase
{
    public function testIsEnabled(): void
    {
        $this->assertFalse(CacheStrategy::NONE->isEnabled());
        $this->assertTrue(CacheStrategy::MEMORY->isEnabled());
        $this->assertTrue(CacheStrategy::REDIS->isEnabled());
        $this->assertTrue(CacheStrategy::FILESYSTEM->isEnabled());
        $this->assertTrue(CacheStrategy::HYBRID->isEnabled());
    }

    public function testIsPersistent(): void
    {
        $this->assertFalse(CacheStrategy::NONE->isPersistent());
        $this->assertFalse(CacheStrategy::MEMORY->isPersistent());
        $this->assertTrue(CacheStrategy::REDIS->isPersistent());
        $this->assertTrue(CacheStrategy::FILESYSTEM->isPersistent());
        $this->assertTrue(CacheStrategy::HYBRID->isPersistent());
    }

    public function testToArray(): void
    {
        // toArray 是实例方法，返回单个枚举项的数组表示
        $array = CacheStrategy::NONE->toArray();

        $this->assertIsArray($array);
        $this->assertCount(2, $array);

        // 验证数组的键值对
        $this->assertArrayHasKey('value', $array);
        $this->assertArrayHasKey('label', $array);

        // 验证值是否正确
        $this->assertSame('none', $array['value']);
        $this->assertSame('不缓存', $array['label']);

        // 测试其他枚举值
        $memoryArray = CacheStrategy::MEMORY->toArray();
        $this->assertSame('memory', $memoryArray['value']);
        $this->assertSame('内存缓存', $memoryArray['label']);

        $redisArray = CacheStrategy::REDIS->toArray();
        $this->assertSame('redis', $redisArray['value']);
        $this->assertSame('Redis缓存', $redisArray['label']);
    }
}
