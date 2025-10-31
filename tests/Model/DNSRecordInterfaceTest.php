<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Model;

use DnsServerBundle\Model\DNSRecordInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(DNSRecordInterface::class)]
final class DNSRecordInterfaceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Interface 测试不需要特别的设置
    }

    public function testInterfaceExists(): void
    {
        $this->assertTrue(interface_exists(DNSRecordInterface::class));
    }

    public function testInterfaceMethodsExist(): void
    {
        $reflection = new \ReflectionClass(DNSRecordInterface::class);

        $this->assertTrue($reflection->hasMethod('getHostname'));
        $this->assertTrue($reflection->hasMethod('getType'));
        $this->assertTrue($reflection->hasMethod('getData'));
        $this->assertTrue($reflection->hasMethod('getTtl'));
    }
}
