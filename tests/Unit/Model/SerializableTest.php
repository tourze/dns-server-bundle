<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Unit\Model;

use DnsServerBundle\Model\Serializable;
use PHPUnit\Framework\TestCase;

class SerializableTest extends TestCase
{
    public function testInterfaceExists(): void
    {
        $this->assertTrue(interface_exists(Serializable::class));
    }

    public function testInterfaceMethodsExist(): void
    {
        $reflection = new \ReflectionClass(Serializable::class);
        
        // 检查接口是否有预期的方法
        $this->assertTrue($reflection->isInterface());
        $methods = $reflection->getMethods();
        $this->assertNotEmpty($methods);
    }
}