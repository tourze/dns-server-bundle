<?php

namespace DnsServerBundle\Tests\Model;

use DnsServerBundle\Exception\InvalidArgumentDnsServerException;
use DnsServerBundle\Model\DNSRecord;
use DnsServerBundle\Model\DNSRecordCollection;
use DnsServerBundle\Model\DNSRecordType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(DNSRecordCollection::class)]
final class DNSRecordCollectionTest extends TestCase
{
    private DNSRecordCollection $collection;

    protected function setUp(): void
    {
        parent::setUp();
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
        $this->assertCount(1, $array);
        $this->assertInstanceOf(DNSRecord::class, $array[0]);
    }

    public function testCurrent(): void
    {
        // 测试空集合
        $this->assertNull($this->collection->current());

        $record = DNSRecord::createFromPrimitives(
            'A',
            'example.com',
            300,
            '192.168.1.1'
        );

        $this->collection[] = $record;
        $this->assertSame($record, $this->collection->current());
    }

    public function testJsonSerialize(): void
    {
        $record = DNSRecord::createFromPrimitives(
            'A',
            'example.com',
            300,
            '192.168.1.1'
        );

        $this->collection[] = $record;

        $jsonData = $this->collection->jsonSerialize();
        $this->assertIsArray($jsonData);
        $this->assertCount(1, $jsonData);
        $this->assertArrayHasKey('record_0', $jsonData);
        // jsonSerialize() now returns serialized arrays, not objects
        $this->assertEquals($record->jsonSerialize(), $jsonData['record_0']);
    }

    public function testKey(): void
    {
        $record = DNSRecord::createFromPrimitives(
            'A',
            'example.com',
            300,
            '192.168.1.1'
        );

        $this->collection[] = $record;

        // 初始键应该是0
        $this->assertSame(0, $this->collection->key());
    }

    public function testNext(): void
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

        $this->assertSame($record1, $collection->current());
        $collection->next();
        $this->assertSame($record2, $collection->current());
    }

    public function testOffsetExists(): void
    {
        $this->assertFalse($this->collection->offsetExists(0));

        $record = DNSRecord::createFromPrimitives(
            'A',
            'example.com',
            300,
            '192.168.1.1'
        );

        $this->collection[] = $record;
        $this->assertTrue($this->collection->offsetExists(0));
        $this->assertFalse($this->collection->offsetExists(1));
    }

    public function testOffsetGet(): void
    {
        $record = DNSRecord::createFromPrimitives(
            'A',
            'example.com',
            300,
            '192.168.1.1'
        );

        $this->collection[] = $record;
        $this->assertSame($record, $this->collection->offsetGet(0));
    }

    public function testOffsetSet(): void
    {
        $record = DNSRecord::createFromPrimitives(
            'A',
            'example.com',
            300,
            '192.168.1.1'
        );

        // 测试使用 offsetSet 添加记录
        $this->collection->offsetSet(0, $record);
        $this->assertCount(1, $this->collection);
        $this->assertSame($record, $this->collection[0]);

        // 测试使用 null 键添加记录
        $record2 = DNSRecord::createFromPrimitives(
            'AAAA',
            'example.com',
            300,
            '2001:db8:85a3::8a2e:370:7334'
        );

        $this->collection->offsetSet(null, $record2);
        $this->assertCount(2, $this->collection);
    }

    public function testOffsetSetWithInvalidValue(): void
    {
        $this->expectException(InvalidArgumentDnsServerException::class);
        $this->expectExceptionMessage('Invalid value');

        $this->collection->offsetSet(0, 'invalid');
    }

    public function testOffsetUnset(): void
    {
        $record = DNSRecord::createFromPrimitives(
            'A',
            'example.com',
            300,
            '192.168.1.1'
        );

        $this->collection[] = $record;
        $this->assertCount(1, $this->collection);

        $this->collection->offsetUnset(0);
        $this->assertCount(0, $this->collection);
    }

    public function testRewind(): void
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

        // 移动到第二个元素
        $collection->next();
        $this->assertSame($record2, $collection->current());

        // 重置到开始
        $collection->rewind();
        $this->assertSame($record1, $collection->current());
    }

    public function testValid(): void
    {
        // 空集合
        $this->assertFalse($this->collection->valid());

        $record = DNSRecord::createFromPrimitives(
            'A',
            'example.com',
            300,
            '192.168.1.1'
        );

        $this->collection[] = $record;
        $this->assertTrue($this->collection->valid());

        // 移动到超出范围
        $this->collection->next();
        $this->assertFalse($this->collection->valid());
    }

    public function testWithUniqueValues(): void
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

        $collection = new DNSRecordCollection($record1, $record2, $record3);

        $unique = $collection->withUniqueValues();
        $this->assertCount(2, $unique);

        // 原集合不变
        $this->assertCount(3, $collection);
    }

    public function testWithUniqueValuesExcluded(): void
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

        $collection = new DNSRecordCollection($record1, $record2, $record3);

        // 这个方法返回只出现一次的值
        $unique = $collection->withUniqueValuesExcluded();
        $this->assertCount(1, $unique);

        // 原集合不变
        $this->assertCount(3, $collection);
    }
}
