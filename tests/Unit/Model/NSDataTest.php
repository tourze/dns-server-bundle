<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Unit\Model;

use DnsServerBundle\Model\NSData;
use DnsServerBundle\Model\Hostname;
use PHPUnit\Framework\TestCase;

class NSDataTest extends TestCase
{
    public function testConstructorWithHostname(): void
    {
        $hostname = new Hostname('ns1.example.com');
        $nsData = new NSData($hostname);
        
        $this->assertSame($hostname, $nsData->getTarget());
    }

    public function testToString(): void
    {
        $hostname = new Hostname('ns1.example.com');
        $nsData = new NSData($hostname);
        
        $this->assertSame('ns1.example.com.', (string) $nsData);
    }

    public function testToArray(): void
    {
        $hostname = new Hostname('ns1.example.com');
        $nsData = new NSData($hostname);
        
        $expected = [
            'target' => 'ns1.example.com.',
        ];
        
        $this->assertSame($expected, $nsData->toArray());
    }
}