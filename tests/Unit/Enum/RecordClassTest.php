<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Unit\Enum;

use DnsServerBundle\Enum\RecordClass;
use PHPUnit\Framework\TestCase;

class RecordClassTest extends TestCase
{
    public function testEnumValues(): void
    {
        $this->assertSame(1, RecordClass::IN->value);
        $this->assertSame(2, RecordClass::CS->value);
        $this->assertSame(3, RecordClass::CH->value);
        $this->assertSame(4, RecordClass::HS->value);
        $this->assertSame(255, RecordClass::ANY->value);
    }

    public function testGetName(): void
    {
        $this->assertSame('IN', RecordClass::IN->getName());
        $this->assertSame('CS', RecordClass::CS->getName());
        $this->assertSame('CH', RecordClass::CH->getName());
        $this->assertSame('HS', RecordClass::HS->getName());
        $this->assertSame('ANY', RecordClass::ANY->getName());
    }

    public function testGetDescription(): void
    {
        $this->assertSame('互联网', RecordClass::IN->getDescription());
        $this->assertSame('CSNET类 (已废弃)', RecordClass::CS->getDescription());
        $this->assertSame('CHAOS类', RecordClass::CH->getDescription());
        $this->assertSame('Hesiod类', RecordClass::HS->getDescription());
        $this->assertSame('任意类', RecordClass::ANY->getDescription());
    }

    public function testGetLabel(): void
    {
        $this->assertSame('互联网', RecordClass::IN->getLabel());
        $this->assertSame('CSNET类 (已废弃)', RecordClass::CS->getLabel());
        $this->assertSame('CHAOS类', RecordClass::CH->getLabel());
        $this->assertSame('Hesiod类', RecordClass::HS->getLabel());
        $this->assertSame('任意类', RecordClass::ANY->getLabel());
    }

    public function testFromName(): void
    {
        $this->assertSame(RecordClass::IN, RecordClass::fromName('IN'));
        $this->assertSame(RecordClass::CS, RecordClass::fromName('CS'));
        $this->assertSame(RecordClass::CH, RecordClass::fromName('CH'));
        $this->assertSame(RecordClass::HS, RecordClass::fromName('HS'));
        $this->assertSame(RecordClass::ANY, RecordClass::fromName('ANY'));
    }

    public function testFromName_withLowercase(): void
    {
        $this->assertSame(RecordClass::IN, RecordClass::fromName('in'));
        $this->assertSame(RecordClass::CS, RecordClass::fromName('cs'));
        $this->assertSame(RecordClass::CH, RecordClass::fromName('ch'));
        $this->assertSame(RecordClass::HS, RecordClass::fromName('hs'));
        $this->assertSame(RecordClass::ANY, RecordClass::fromName('any'));
    }

    public function testFromName_withInvalidName_returnsNull(): void
    {
        $this->assertNull(RecordClass::fromName('INVALID'));
        $this->assertNull(RecordClass::fromName(''));
        $this->assertNull(RecordClass::fromName('123'));
    }
}