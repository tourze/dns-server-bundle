<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Unit\Model;

use DnsServerBundle\Model\PTRData;
use DnsServerBundle\Model\Hostname;
use PHPUnit\Framework\TestCase;

class PTRDataTest extends TestCase
{
    public function testConstructorWithHostname(): void
    {
        $hostname = new Hostname('example.com');
        $ptrData = new PTRData($hostname);
        
        $this->assertSame($hostname, $ptrData->getHostname());
    }

    public function testToString(): void
    {
        $hostname = new Hostname('example.com');
        $ptrData = new PTRData($hostname);
        
        $this->assertSame('example.com.', (string) $ptrData);
    }

    public function testToArray(): void
    {
        $hostname = new Hostname('example.com');
        $ptrData = new PTRData($hostname);
        
        $expected = [
            'hostname' => 'example.com.',
        ];
        
        $this->assertSame($expected, $ptrData->toArray());
    }
}