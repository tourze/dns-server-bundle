<?php

namespace DnsServerBundle\Tests\Model;

use DnsServerBundle\Model\DNSRecord;
use DnsServerBundle\Model\DNSRecordCollection;
use DnsServerBundle\Model\DNSRecordType;
use PHPUnit\Framework\TestCase;

class DNSRecordCollectionTest extends TestCase
{
    private DNSRecordCollection $collection;
    
    protected function setUp(): void
    {
        $this->collection = new DNSRecordCollection();
    }
    
    public function testConstructor(): void
    {
        $this->assertCount(0, $this->collection);
    }
    
    public function testAddRecordViaArrayAccess(): void
    {
        $record = DNSRecord::createFromPrimitives(
            'A',
            'example.com',
            300,
            '192.168.1.1'
        );
        
        $this->collection[] = $record;
        
        $this->assertCount(1, $this->collection);
        $this->assertTrue($this->collection->has($record));
    }
    
    public function testConstructorWithMultipleRecords(): void
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
            '192.168.1.2'
        );
        
        $collection = new DNSRecordCollection($record1, $record2);
        
        $this->assertCount(2, $collection);
        $this->assertTrue($collection->has($record1));
        $this->assertTrue($collection->has($record2));
    }
    
    public function testRemoveRecordViaArrayAccess(): void
    {
        $record = DNSRecord::createFromPrimitives(
            'A',
            'example.com',
            300,
            '192.168.1.1'
        );
        
        $this->collection[] = $record;
        $this->assertCount(1, $this->collection);
        
        unset($this->collection[0]);
        $this->assertCount(0, $this->collection);
        $this->assertFalse($this->collection->has($record));
    }
    
    public function testFilterValues(): void
    {
        $record1 = DNSRecord::createFromPrimitives(
            'A',
            'example.com',
            300,
            '192.168.1.1'
        );
        
        $record2 = DNSRecord::createFromPrimitives(
            'AAAA',
            'example.com',
            300,
            '2001:db8:85a3::8a2e:370:7334'
        );
        
        $collection = new DNSRecordCollection($record1, $record2);
        
        // 使用withUniqueValues过滤
        $filtered = $collection->withUniqueValues();
        
        $this->assertInstanceOf(DNSRecordCollection::class, $filtered);
        $this->assertCount(2, $filtered);
        
        // 原集合保持不变
        $this->assertCount(2, $collection);
    }
    
    public function testFilteredByType(): void
    {
        $record1 = DNSRecord::createFromPrimitives(
            'A',
            'example.com',
            300,
            '192.168.1.1'
        );
        
        $record2 = DNSRecord::createFromPrimitives(
            'AAAA',
            'example.com',
            300,
            '2001:db8:85a3::8a2e:370:7334'
        );
        
        $collection = new DNSRecordCollection($record1, $record2);
        
        $filtered = $collection->filteredByType(DNSRecordType::createA());
        
        $this->assertInstanceOf(DNSRecordCollection::class, $filtered);
        $this->assertCount(1, $filtered);
        $this->assertTrue($filtered->has($record1));
        $this->assertFalse($filtered->has($record2));
    }
    
    public function testPickFirst(): void
    {
        $record1 = DNSRecord::createFromPrimitives(
            'A',
            'example.com',
            300,
            '192.168.1.1'
        );
        
        $record2 = DNSRecord::createFromPrimitives(
            'AAAA',
            'example.com',
            300,
            '2001:db8:85a3::8a2e:370:7334'
        );
        
        $collection = new DNSRecordCollection($record1, $record2);
        
        $first = $collection->pickFirst();
        
        $this->assertSame($record1, $first);
    }
    
    public function testPickFirstWithEmptyCollection(): void
    {
        $this->assertNull($this->collection->pickFirst());
    }
    
    public function testHas(): void
    {
        $record = DNSRecord::createFromPrimitives(
            'A',
            'example.com',
            300,
            '192.168.1.1'
        );
        
        $this->assertFalse($this->collection->has($record));
        
        $this->collection[] = $record;
        $this->assertTrue($this->collection->has($record));
    }
    
    public function testCount(): void
    {
        $this->assertCount(0, $this->collection);
        
        $record = DNSRecord::createFromPrimitives(
            'A',
            'example.com',
            300,
            '192.168.1.1'
        );
        
        $this->collection[] = $record;
        $this->assertCount(1, $this->collection);
    }
    
    public function testIsEmpty(): void
    {
        $this->assertTrue($this->collection->isEmpty());
        
        $record = DNSRecord::createFromPrimitives(
            'A',
            'example.com',
            300,
            '192.168.1.1'
        );
        
        $this->collection[] = $record;
        $this->assertFalse($this->collection->isEmpty());
    }
    
    public function testIterator(): void
    {
        $record1 = DNSRecord::createFromPrimitives(
            'A',
            'example.com',
            300,
            '192.168.1.1'
        );
        
        $record2 = DNSRecord::createFromPrimitives(
            'AAAA',
            'example.com',
            300,
            '2001:db8:85a3::8a2e:370:7334'
        );
        
        $collection = new DNSRecordCollection($record1, $record2);
        
        $records = [];
        foreach ($collection as $record) {
            $records[] = $record;
        }
        
        $this->assertCount(2, $records);
        $this->assertContains($record1, $records);
        $this->assertContains($record2, $records);
    }
    
    public function testToArray(): void
    {
        $record = DNSRecord::createFromPrimitives(
            'A',
            'example.com',
            300,
            '192.168.1.1'
        );
        
        $this->collection[] = $record;
        
        $array = $this->collection->toArray();
        
        $this->assertIsArray($array);
        $this->assertCount(1, $array);
        $this->assertInstanceOf(DNSRecord::class, $array[0]);
    }
} 