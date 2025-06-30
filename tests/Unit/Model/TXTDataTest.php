<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Unit\Model;

use DnsServerBundle\Model\TXTData;
use PHPUnit\Framework\TestCase;

class TXTDataTest extends TestCase
{
    public function testConstructorAndGetValue(): void
    {
        $value = 'v=spf1 include:_spf.google.com ~all';
        $txtData = new TXTData($value);
        
        $this->assertSame($value, $txtData->getValue());
    }

    public function testToString(): void
    {
        $value = 'v=spf1 include:_spf.google.com ~all';
        $txtData = new TXTData($value);
        
        $this->assertSame($value, (string) $txtData);
    }

    public function testToArray(): void
    {
        $value = 'v=spf1 include:_spf.google.com ~all';
        $txtData = new TXTData($value);
        
        $expected = [
            'value' => $value,
        ];
        
        $this->assertSame($expected, $txtData->toArray());
    }

    public function testSerializationUnserialization(): void
    {
        $value = 'v=spf1 include:_spf.google.com ~all';
        $original = new TXTData($value);
        
        $serialized = serialize($original);
        $unserialized = unserialize($serialized);
        
        $this->assertSame($original->getValue(), $unserialized->getValue());
        $this->assertSame((string) $original, (string) $unserialized);
    }

    public function testWithEmptyValue(): void
    {
        $txtData = new TXTData('');
        
        $this->assertSame('', $txtData->getValue());
        $this->assertSame('', (string) $txtData);
    }
}