<?php

namespace DnsServerBundle\Tests\Enum;

use DnsServerBundle\Enum\DnsProtocolEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(DnsProtocolEnum::class)]
final class DnsProtocolEnumTest extends AbstractEnumTestCase
{
    public function testEnumExists(): void
    {
        $this->assertTrue(enum_exists(DnsProtocolEnum::class));
    }

    public function testEnumValues(): void
    {
        $this->assertSame('UDP', DnsProtocolEnum::UDP->name);
        $this->assertSame('TCP', DnsProtocolEnum::TCP->name);
        $this->assertSame('DOH', DnsProtocolEnum::DOH->name);
        $this->assertSame('DOT', DnsProtocolEnum::DOT->name);
    }

    public function testTryFromWithValidValue(): void
    {
        $this->assertEquals(DnsProtocolEnum::UDP, DnsProtocolEnum::tryFrom('udp'));
        $this->assertEquals(DnsProtocolEnum::TCP, DnsProtocolEnum::tryFrom('tcp'));
        $this->assertEquals(DnsProtocolEnum::DOH, DnsProtocolEnum::tryFrom('doh'));
        $this->assertEquals(DnsProtocolEnum::DOT, DnsProtocolEnum::tryFrom('dot'));
    }

    public function testValueEquality(): void
    {
        $udp1 = DnsProtocolEnum::UDP;
        $udp2 = DnsProtocolEnum::UDP;
        $tcp = DnsProtocolEnum::TCP;

        $this->assertSame($udp1, $udp2);
        $this->assertNotEquals($udp1, $tcp);
    }

    public function testGetLabel(): void
    {
        $this->assertSame('UDP', DnsProtocolEnum::UDP->getLabel());
        $this->assertSame('TCP', DnsProtocolEnum::TCP->getLabel());
        $this->assertSame('DNS over HTTPS', DnsProtocolEnum::DOH->getLabel());
        $this->assertSame('DNS over TLS', DnsProtocolEnum::DOT->getLabel());
    }

    public function testToArray(): void
    {
        // toArray 是实例方法，返回单个枚举项的数组表示
        $array = DnsProtocolEnum::UDP->toArray();

        $this->assertIsArray($array);
        $this->assertCount(2, $array);

        // 验证数组的键值对
        $this->assertArrayHasKey('value', $array);
        $this->assertArrayHasKey('label', $array);

        // 验证值是否正确
        $this->assertSame('udp', $array['value']);
        $this->assertSame('UDP', $array['label']);

        // 测试其他枚举值
        $tcpArray = DnsProtocolEnum::TCP->toArray();
        $this->assertSame('tcp', $tcpArray['value']);
        $this->assertSame('TCP', $tcpArray['label']);

        $dohArray = DnsProtocolEnum::DOH->toArray();
        $this->assertSame('doh', $dohArray['value']);
        $this->assertSame('DNS over HTTPS', $dohArray['label']);
    }
}
