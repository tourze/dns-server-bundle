<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Unit\Model;

use DnsServerBundle\Exception\InvalidArgumentDnsServerException;
use DnsServerBundle\Model\CAAData;
use PHPUnit\Framework\TestCase;

class CAADataTest extends TestCase
{
    public function testConstructorWithValidData(): void
    {
        $caaData = new CAAData(128, 'issue', 'ca.example.com');
        
        $this->assertSame(128, $caaData->getFlags());
        $this->assertSame('issue', $caaData->getTag());
        $this->assertSame('ca.example.com', $caaData->getValue());
    }

    public function testConstructorWithNullValue(): void
    {
        $caaData = new CAAData(0, 'iodef');
        
        $this->assertSame(0, $caaData->getFlags());
        $this->assertSame('iodef', $caaData->getTag());
        $this->assertNull($caaData->getValue());
    }

    public function testToString(): void
    {
        $caaData = new CAAData(128, 'issue', 'ca.example.com');
        $expected = '128 issue "ca.example.com"';
        
        $this->assertSame($expected, (string) $caaData);
    }

    public function testToStringWithNullValue(): void
    {
        $caaData = new CAAData(0, 'iodef');
        $expected = '0 iodef ""';
        
        $this->assertSame($expected, (string) $caaData);
    }

    public function testToArray(): void
    {
        $caaData = new CAAData(128, 'issue', 'ca.example.com');
        $expected = [
            'flags' => 128,
            'tag' => 'issue',
            'value' => 'ca.example.com',
        ];
        
        $this->assertSame($expected, $caaData->toArray());
    }

    public function testNormalizeValueRemovesQuotes(): void
    {
        $caaData = new CAAData(0, 'issue', '"ca.example.com"');
        
        $this->assertSame('ca.example.com', $caaData->getValue());
    }

    public function testNormalizeValueTrimsWhitespace(): void
    {
        $caaData = new CAAData(0, 'issue', '  ca.example.com  ');
        
        $this->assertSame('ca.example.com', $caaData->getValue());
    }

    public function testNormalizeValueThrowsExceptionOnInvalidValue(): void
    {
        $this->expectException(InvalidArgumentDnsServerException::class);
        $this->expectExceptionMessage('ca.example.com with spaces is not a valid CAA value');
        
        new CAAData(0, 'issue', 'ca.example.com with spaces');
    }

    public function testSerializationUnserialization(): void
    {
        $original = new CAAData(128, 'issue', 'ca.example.com');
        $serialized = serialize($original);
        $unserialized = unserialize($serialized);
        
        $this->assertSame($original->getFlags(), $unserialized->getFlags());
        $this->assertSame($original->getTag(), $unserialized->getTag());
        $this->assertSame($original->getValue(), $unserialized->getValue());
    }
}