<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Unit\Enum;

use DnsServerBundle\Enum\Protocol;
use PHPUnit\Framework\TestCase;

class ProtocolTest extends TestCase
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
}