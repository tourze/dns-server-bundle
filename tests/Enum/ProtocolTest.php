<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Enum;

use DnsServerBundle\Enum\Protocol;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(Protocol::class)]
final class ProtocolTest extends AbstractEnumTestCase
{
    public function testEnumValues(): void
    {
        $this->assertSame('udp', Protocol::UDP->value);
        $this->assertSame('tcp', Protocol::TCP->value);
    }

    public function testGetDescription(): void
    {
        $this->assertSame('UDP协议', Protocol::UDP->getDescription());
        $this->assertSame('TCP协议', Protocol::TCP->getDescription());
    }

    public function testGetLabel(): void
    {
        $this->assertSame('UDP协议', Protocol::UDP->getLabel());
        $this->assertSame('TCP协议', Protocol::TCP->getLabel());
    }

    public function testGetDefaultPort(): void
    {
        $this->assertSame(53, Protocol::UDP->getDefaultPort());
        $this->assertSame(53, Protocol::TCP->getDefaultPort());
    }

    public function testIsUdp(): void
    {
        $this->assertTrue(Protocol::UDP->isUdp());
        $this->assertFalse(Protocol::TCP->isUdp());
    }

    public function testIsTcp(): void
    {
        $this->assertFalse(Protocol::UDP->isTcp());
        $this->assertTrue(Protocol::TCP->isTcp());
    }

    public function testToArray(): void
    {
        // toArray 是实例方法，返回单个枚举项的数组表示
        $array = Protocol::UDP->toArray();

        $this->assertIsArray($array);
        $this->assertCount(2, $array);

        // 验证数组的键值对
        $this->assertArrayHasKey('value', $array);
        $this->assertArrayHasKey('label', $array);

        // 验证值是否正确
        $this->assertSame('udp', $array['value']);
        $this->assertSame('UDP协议', $array['label']);

        // 测试其他枚举值
        $tcpArray = Protocol::TCP->toArray();
        $this->assertSame('tcp', $tcpArray['value']);
        $this->assertSame('TCP协议', $tcpArray['label']);
    }
}
