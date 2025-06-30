<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Unit\Model;

use DnsServerBundle\Model\EntityAbstract;
use PHPUnit\Framework\TestCase;

class EntityAbstractTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(EntityAbstract::class));
    }

    public function testIsAbstractClass(): void
    {
        $reflection = new \ReflectionClass(EntityAbstract::class);
        $this->assertTrue($reflection->isAbstract());
    }

    public function testIsAbstractEntity(): void
    {
        $reflection = new \ReflectionClass(EntityAbstract::class);
        
        // 确认它是抽象类
        $this->assertTrue($reflection->isAbstract());
        // 确认这是一个简单的抽象基类
        $this->assertEmpty($reflection->getMethods());
    }
}