<?php

namespace DnsServerBundle\Tests\Model;

use DnsServerBundle\Model\DNSRecord;
use DnsServerBundle\Model\DNSRecordType;
use DnsServerBundle\Model\Hostname;
use DnsServerBundle\Model\IPAddress;
use DnsServerBundle\Model\MXData;
use PHPUnit\Framework\TestCase;

class DNSRecordTest extends TestCase
{
    public function testConstructor(): void
    {
        $recordType = DNSRecordType::createA();
        $hostname = new Hostname('example.com');
        $ttl = 300;
        $ipAddress = new IPAddress('192.168.1.1');
        
        $record = new DNSRecord($recordType, $hostname, $ttl, $ipAddress);
        
        $this->assertSame($recordType, $record->getType());
        $this->assertSame($hostname, $record->getHostname());
        $this->assertSame($ttl, $record->getTTL());
        $this->assertSame($ipAddress, $record->getIPAddress());
        $this->assertSame('IN', $record->getClass());
        $this->assertNull($record->getData());
    }
    
    public function testCreateFromPrimitives(): void
    {
        $record = DNSRecord::createFromPrimitives(
            'A',
            'example.com',
            300,
            '192.168.1.1'
        );
        
        $this->assertSame('A', (string)$record->getType());
        $this->assertSame('example.com.', (string)$record->getHostname());
        $this->assertSame(300, $record->getTTL());
        $this->assertSame('192.168.1.1', (string)$record->getIPAddress());
        $this->assertSame('IN', $record->getClass());
    }
    
    public function testCreateMXRecordFromPrimitives(): void
    {
        $record = DNSRecord::createFromPrimitives(
            'MX',
            'example.com',
            300,
            null,
            'IN',
            '10 mail.example.com'
        );
        
        $this->assertSame('MX', (string)$record->getType());
        $this->assertSame('example.com.', (string)$record->getHostname());
        $this->assertSame(300, $record->getTTL());
        $this->assertNull($record->getIPAddress());
        $this->assertSame('IN', $record->getClass());
        $this->assertInstanceOf(MXData::class, $record->getData());
    }
    
    public function testToArray(): void
    {
        $record = DNSRecord::createFromPrimitives(
            'A',
            'example.com',
            300,
            '192.168.1.1'
        );
        
        $array = $record->toArray();
        
        $this->assertIsArray($array);
        $this->assertArrayHasKey('hostname', $array);
        $this->assertArrayHasKey('type', $array);
        $this->assertArrayHasKey('TTL', $array);
        $this->assertArrayHasKey('class', $array);
        $this->assertArrayHasKey('IPAddress', $array);
        
        $this->assertSame('example.com.', $array['hostname']);
        $this->assertSame('A', $array['type']);
        $this->assertSame(300, $array['TTL']);
        $this->assertSame('IN', $array['class']);
        $this->assertSame('192.168.1.1', $array['IPAddress']);
    }
    
    public function testEquals(): void
    {
        $record1 = DNSRecord::createFromPrimitives(
            'A',
            'example.com',
            300,
            '192.168.1.1'
        );
        
        $record2 = DNSRecord::createFromPrimitives(
            'A',
            'example.com',
            300,
            '192.168.1.1'
        );
        
        $record3 = DNSRecord::createFromPrimitives(
            'A',
            'example.com',
            300,
            '192.168.1.2'
        );
        
        $this->assertTrue($record1->equals($record2));
        $this->assertFalse($record1->equals($record3));
    }
    
    public function testSetData(): void
    {
        $recordType = DNSRecordType::createMX();
        $hostname = new Hostname('example.com');
        $ttl = 300;
        
        $record = new DNSRecord($recordType, $hostname, $ttl);
        
        $mxData = new MXData(new Hostname('mail.example.com'), 10);
        $record->setData($mxData);
        
        $this->assertSame($mxData, $record->getData());
    }
    
    public function testSetTTL(): void
    {
        $record = DNSRecord::createFromPrimitives(
            'A',
            'example.com',
            300,
            '192.168.1.1'
        );
        
        $record->setTTL(600);
        
        $this->assertSame(600, $record->getTTL());
    }
    
    public function testSerializeAndUnserialize(): void
    {
        $original = DNSRecord::createFromPrimitives(
            'A',
            'example.com',
            300,
            '192.168.1.1'
        );
        
        // 序列化和反序列化
        $serialized = serialize($original);
        $unserialized = unserialize($serialized);
        
        $this->assertInstanceOf(DNSRecord::class, $unserialized);
        $this->assertTrue($original->equals($unserialized));
    }
    
    public function testJsonSerialize(): void
    {
        $record = DNSRecord::createFromPrimitives(
            'A',
            'example.com',
            300,
            '192.168.1.1'
        );
        
        $json = json_encode($record);
        $decoded = json_decode($json, true);
        
        $this->assertIsArray($decoded);
        $this->assertArrayHasKey('hostname', $decoded);
        $this->assertArrayHasKey('type', $decoded);
        $this->assertArrayHasKey('TTL', $decoded);
        $this->assertArrayHasKey('IPAddress', $decoded);
        
        $this->assertSame('example.com.', $decoded['hostname']);
        $this->assertSame('A', $decoded['type']);
        $this->assertSame(300, $decoded['TTL']);
        $this->assertSame('192.168.1.1', $decoded['IPAddress']);
    }
} 