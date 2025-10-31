<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Model;

use DnsServerBundle\Model\Hostname;
use DnsServerBundle\Model\SOAData;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(SOAData::class)]
final class SOADataTest extends TestCase
{
    private SOAData $soaData;

    protected function setUp(): void
    {
        parent::setUp();

        $mname = new Hostname('ns1.example.com');
        $rname = new Hostname('admin.example.com');
        $this->soaData = new SOAData($mname, $rname, 2023060101, 3600, 1800, 604800, 86400);
    }

    public function testConstructorAndGetters(): void
    {
        $mname = new Hostname('ns1.example.com');
        $rname = new Hostname('admin.example.com');

        $this->assertEquals($mname, $this->soaData->getMname());
        $this->assertEquals($rname, $this->soaData->getRname());
        $this->assertSame(2023060101, $this->soaData->getSerial());
        $this->assertSame(3600, $this->soaData->getRefresh());
        $this->assertSame(1800, $this->soaData->getRetry());
        $this->assertSame(604800, $this->soaData->getExpire());
        $this->assertSame(86400, $this->soaData->getMinTTL());
    }

    public function testToString(): void
    {
        $expected = 'ns1.example.com. admin.example.com. 2023060101 3600 1800 604800 86400';
        $this->assertSame($expected, (string) $this->soaData);
    }

    public function testToArray(): void
    {
        $expected = [
            'mname' => 'ns1.example.com.',
            'rname' => 'admin.example.com.',
            'serial' => 2023060101,
            'refresh' => 3600,
            'retry' => 1800,
            'expire' => 604800,
            'minimumTTL' => 86400,
        ];

        $this->assertSame($expected, $this->soaData->toArray());
    }

    public function testSerializationUnserialization(): void
    {
        $serialized = serialize($this->soaData);
        $unserialized = unserialize($serialized);
        $this->assertInstanceOf(SOAData::class, $unserialized);

        $this->assertEquals($this->soaData->getMname(), $unserialized->getMname());
        $this->assertEquals($this->soaData->getRname(), $unserialized->getRname());
        $this->assertSame($this->soaData->getSerial(), $unserialized->getSerial());
        $this->assertSame($this->soaData->getRefresh(), $unserialized->getRefresh());
        $this->assertSame($this->soaData->getRetry(), $unserialized->getRetry());
        $this->assertSame($this->soaData->getExpire(), $unserialized->getExpire());
        $this->assertSame($this->soaData->getMinTTL(), $unserialized->getMinTTL());
    }
}
