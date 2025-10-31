<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Model;

use DnsServerBundle\Model\Serializable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(Serializable::class)]
final class SerializableTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Interface 测试不需要特别的设置
    }

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
