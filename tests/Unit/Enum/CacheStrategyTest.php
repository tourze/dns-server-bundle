<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Unit\Enum;

use DnsServerBundle\Enum\CacheStrategy;
use PHPUnit\Framework\TestCase;

class CacheStrategyTest extends TestCase
{
    public function testEnumValues(): void
    {
        $this->assertSame('none', CacheStrategy::NONE->value);
        $this->assertSame('memory', CacheStrategy::MEMORY->value);
        $this->assertSame('redis', CacheStrategy::REDIS->value);
        $this->assertSame('file', CacheStrategy::FILESYSTEM->value);
        $this->assertSame('hybrid', CacheStrategy::HYBRID->value);
    }

    public function testGetDescription(): void
    {
        $this->assertSame('不缓存', CacheStrategy::NONE->getDescription());
        $this->assertSame('内存缓存', CacheStrategy::MEMORY->getDescription());
        $this->assertSame('Redis缓存', CacheStrategy::REDIS->getDescription());
        $this->assertSame('文件系统缓存', CacheStrategy::FILESYSTEM->getDescription());
        $this->assertSame('混合缓存 (内存+持久化)', CacheStrategy::HYBRID->getDescription());
    }

    public function testGetLabel(): void
    {
        $this->assertSame('不缓存', CacheStrategy::NONE->getLabel());
        $this->assertSame('内存缓存', CacheStrategy::MEMORY->getLabel());
        $this->assertSame('Redis缓存', CacheStrategy::REDIS->getLabel());
        $this->assertSame('文件系统缓存', CacheStrategy::FILESYSTEM->getLabel());
        $this->assertSame('混合缓存 (内存+持久化)', CacheStrategy::HYBRID->getLabel());
    }

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
}