<?php

namespace DnsServerBundle\Tests\Model;

use DnsServerBundle\Exception\InvalidArgumentDnsServerException;
use DnsServerBundle\Model\DNSRecordType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(DNSRecordType::class)]
final class DNSRecordTypeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Model 测试不需要特别的设置
    }

    public function testConstructorWithValidType(): void
    {
        $recordType = new DNSRecordType(DNSRecordType::TYPE_A);
        $this->assertSame(DNSRecordType::TYPE_A, (string) $recordType);
    }

    public function testConstructorWithInvalidType(): void
    {
        $this->expectException(InvalidArgumentDnsServerException::class);
        new DNSRecordType('INVALID_TYPE');
    }

    public function testCreateFromInt(): void
    {
        $recordType = DNSRecordType::createFromInt(1);
        $this->assertSame(DNSRecordType::TYPE_A, (string) $recordType);

        $recordType = DNSRecordType::createFromInt(5);
        $this->assertSame(DNSRecordType::TYPE_CNAME, (string) $recordType);

        $recordType = DNSRecordType::createFromInt(28);
        $this->assertSame(DNSRecordType::TYPE_AAAA, (string) $recordType);
    }

    public function testCreateFromIntWithInvalidCode(): void
    {
        $this->expectException(InvalidArgumentDnsServerException::class);
        DNSRecordType::createFromInt(999);
    }

    public function testCreateFromString(): void
    {
        $recordType = DNSRecordType::createFromString(DNSRecordType::TYPE_A);
        $this->assertSame(DNSRecordType::TYPE_A, (string) $recordType);
    }

    public function testToInt(): void
    {
        $recordType = new DNSRecordType(DNSRecordType::TYPE_A);
        $this->assertSame(1, $recordType->toInt());

        $recordType = new DNSRecordType(DNSRecordType::TYPE_AAAA);
        $this->assertSame(28, $recordType->toInt());
    }

    public function testIsA(): void
    {
        $recordType = new DNSRecordType(DNSRecordType::TYPE_A);
        $this->assertTrue($recordType->isA('A'));
        $this->assertTrue($recordType->isA('a'));
        $this->assertFalse($recordType->isA('AAAA'));
    }

    public function testEquals(): void
    {
        $recordType1 = new DNSRecordType(DNSRecordType::TYPE_A);
        $recordType2 = new DNSRecordType(DNSRecordType::TYPE_A);
        $recordType3 = new DNSRecordType(DNSRecordType::TYPE_AAAA);

        $this->assertTrue($recordType1->equals($recordType2));
        $this->assertFalse($recordType1->equals($recordType3));
    }

    public function testStaticFactoryMethods(): void
    {
        $this->assertSame(DNSRecordType::TYPE_A, (string) DNSRecordType::createA());
        $this->assertSame(DNSRecordType::TYPE_AAAA, (string) DNSRecordType::createAAAA());
        $this->assertSame(DNSRecordType::TYPE_CNAME, (string) DNSRecordType::createCNAME());
        $this->assertSame(DNSRecordType::TYPE_MX, (string) DNSRecordType::createMX());
        $this->assertSame(DNSRecordType::TYPE_NS, (string) DNSRecordType::createNS());
        $this->assertSame(DNSRecordType::TYPE_TXT, (string) DNSRecordType::createTXT());
        $this->assertSame(DNSRecordType::TYPE_SOA, (string) DNSRecordType::createSOA());
        $this->assertSame(DNSRecordType::TYPE_PTR, (string) DNSRecordType::createPTR());
        $this->assertSame(DNSRecordType::TYPE_CAA, (string) DNSRecordType::createCAA());
        $this->assertSame(DNSRecordType::TYPE_SRV, (string) DNSRecordType::createSRV());
        $this->assertSame(DNSRecordType::TYPE_A6, (string) DNSRecordType::createA6());
        $this->assertSame(DNSRecordType::TYPE_ANY, (string) DNSRecordType::createANY());
    }

    public function testToString(): void
    {
        $recordType = new DNSRecordType(DNSRecordType::TYPE_A);
        $this->assertSame(DNSRecordType::TYPE_A, (string) $recordType);
    }
}
