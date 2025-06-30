<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Unit\Model;

use DnsServerBundle\Model\CNAMEData;
use DnsServerBundle\Model\Hostname;
use PHPUnit\Framework\TestCase;

class CNAMEDataTest extends TestCase
{
    public function testConstructorWithHostname(): void
    {
        $hostname = new Hostname('www.example.com');
        $cnameData = new CNAMEData($hostname);
        
        $this->assertSame($hostname, $cnameData->getHostname());
    }

    public function testToString(): void
    {
        $hostname = new Hostname('www.example.com');
        $cnameData = new CNAMEData($hostname);
        
        $this->assertSame('www.example.com.', (string) $cnameData);
    }

    public function testToArray(): void
    {
        $hostname = new Hostname('www.example.com');
        $cnameData = new CNAMEData($hostname);
        
        $expected = [
            'hostname' => 'www.example.com.',
        ];
        
        $this->assertSame($expected, $cnameData->toArray());
    }

    public function testSerializationUnserialization(): void
    {
        $hostname = new Hostname('www.example.com');
        $original = new CNAMEData($hostname);
        
        $serialized = serialize($original);
        $unserialized = unserialize($serialized);
        
        $this->assertEquals($original->getHostname(), $unserialized->getHostname());
        $this->assertSame((string) $original, (string) $unserialized);
    }
}