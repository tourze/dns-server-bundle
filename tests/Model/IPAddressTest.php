<?php

namespace DnsServerBundle\Tests\Model;

use DnsServerBundle\Exception\InvalidArgumentDnsServerException;
use DnsServerBundle\Model\IPAddress;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(IPAddress::class)]
final class IPAddressTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Model 测试不需要特别的设置
    }

    public function testConstructorWithValidIPv4(): void
    {
        $ip = new IPAddress('192.168.1.1');
        $this->assertSame('192.168.1.1', (string) $ip);
        $this->assertTrue($ip->isIPv4());
        $this->assertFalse($ip->isIPv6());
    }

    public function testConstructorWithValidIPv6(): void
    {
        $ip = new IPAddress('2001:db8:85a3::8a2e:370:7334');
        $this->assertSame('2001:db8:85a3::8a2e:370:7334', (string) $ip);
        $this->assertTrue($ip->isIPv6());
        $this->assertFalse($ip->isIPv4());
    }

    public function testConstructorWithInvalidIP(): void
    {
        $this->expectException(InvalidArgumentDnsServerException::class);
        new IPAddress('invalid-ip');
    }

    public function testCreateFromString(): void
    {
        $ip = IPAddress::createFromString('192.168.1.1');
        $this->assertSame('192.168.1.1', (string) $ip);

        $ip = IPAddress::createFromString('2001:db8:85a3::8a2e:370:7334');
        $this->assertSame('2001:db8:85a3::8a2e:370:7334', (string) $ip);
    }

    public function testToString(): void
    {
        $ip = new IPAddress('192.168.1.1');
        $this->assertSame('192.168.1.1', (string) $ip);
    }

    public function testEquals(): void
    {
        $ip1 = new IPAddress('192.168.1.1');
        $ip2 = new IPAddress('192.168.1.1');
        $ip3 = new IPAddress('192.168.1.2');

        $this->assertTrue($ip1->equals($ip2));
        $this->assertFalse($ip1->equals($ip3));
    }

    public function testIsIPv4(): void
    {
        $ipv4 = new IPAddress('192.168.1.1');
        $ipv6 = new IPAddress('2001:0db8:85a3:0000:0000:8a2e:0370:7334');

        $this->assertTrue($ipv4->isIPv4());
        $this->assertFalse($ipv6->isIPv4());
    }

    public function testIsIPv6(): void
    {
        $ipv4 = new IPAddress('192.168.1.1');
        $ipv6 = new IPAddress('2001:0db8:85a3:0000:0000:8a2e:0370:7334');

        $this->assertFalse($ipv4->isIPv6());
        $this->assertTrue($ipv6->isIPv6());
    }

    public function testIPv6Formats(): void
    {
        // 测试不同的IPv6格式
        $fullIPv6 = new IPAddress('2001:0db8:85a3:0000:0000:8a2e:0370:7334');
        $compressedIPv6 = new IPAddress('2001:db8:85a3::8a2e:370:7334');

        $this->assertTrue($fullIPv6->isIPv6());
        $this->assertTrue($compressedIPv6->isIPv6());
        $this->assertFalse($fullIPv6->isIPv4());
        $this->assertFalse($compressedIPv6->isIPv4());
    }
}
