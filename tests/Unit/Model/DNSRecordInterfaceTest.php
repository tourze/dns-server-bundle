<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Unit\Model;

use DnsServerBundle\Model\DNSRecordInterface;
use DnsServerBundle\Model\DNSRecord;
use DnsServerBundle\Enum\RecordType;
use DnsServerBundle\Model\Hostname;
use PHPUnit\Framework\TestCase;

class DNSRecordInterfaceTest extends TestCase
{
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