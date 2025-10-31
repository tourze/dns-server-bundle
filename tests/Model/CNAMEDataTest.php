<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Model;

use DnsServerBundle\Model\CNAMEData;
use DnsServerBundle\Model\Hostname;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(CNAMEData::class)]
final class CNAMEDataTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Model 测试不需要特别的设置
    }

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
        $this->assertInstanceOf(CNAMEData::class, $unserialized);

        $this->assertEquals($original->getHostname(), $unserialized->getHostname());
        $this->assertSame((string) $original, (string) $unserialized);
    }
}
