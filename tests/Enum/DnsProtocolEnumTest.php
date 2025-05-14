<?php

namespace DnsServerBundle\Tests\Enum;

use DnsServerBundle\Enum\DnsProtocolEnum;
use PHPUnit\Framework\TestCase;

class DnsProtocolEnumTest extends TestCase
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

    public function testTryFromWithInvalidValue(): void
    {
        $this->assertNull(DnsProtocolEnum::tryFrom('INVALID'));
        $this->assertNull(DnsProtocolEnum::tryFrom(''));
    }

    public function testValueEquality(): void
    {
        $udp1 = DnsProtocolEnum::UDP;
        $udp2 = DnsProtocolEnum::UDP;
        $tcp = DnsProtocolEnum::TCP;

        $this->assertTrue($udp1 === $udp2);
        $this->assertFalse($udp1 === $tcp);
    }
} 