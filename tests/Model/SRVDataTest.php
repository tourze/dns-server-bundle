<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Model;

use DnsServerBundle\Model\Hostname;
use DnsServerBundle\Model\SRVData;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(SRVData::class)]
final class SRVDataTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Model 测试不需要特别的设置
    }

    public function testConstructorAndGetters(): void
    {
        $target = new Hostname('target.example.com');
        $srvData = new SRVData(10, 20, 80, $target);

        $this->assertSame(10, $srvData->getPriority());
        $this->assertSame(20, $srvData->getWeight());
        $this->assertSame(80, $srvData->getPort());
        $this->assertSame($target, $srvData->getTarget());
    }

    public function testToString(): void
    {
        $target = new Hostname('target.example.com');
        $srvData = new SRVData(10, 20, 80, $target);

        $this->assertSame('10 20 80 target.example.com.', (string) $srvData);
    }

    public function testToArray(): void
    {
        $target = new Hostname('target.example.com');
        $srvData = new SRVData(10, 20, 80, $target);

        $expected = [
            'priority' => 10,
            'weight' => 20,
            'port' => 80,
            'target' => 'target.example.com.',
        ];

        $this->assertSame($expected, $srvData->toArray());
    }

    public function testSerializationUnserialization(): void
    {
        $target = new Hostname('target.example.com');
        $original = new SRVData(10, 20, 80, $target);

        $serialized = serialize($original);
        $unserialized = unserialize($serialized);
        $this->assertInstanceOf(SRVData::class, $unserialized);

        $this->assertSame($original->getPriority(), $unserialized->getPriority());
        $this->assertSame($original->getWeight(), $unserialized->getWeight());
        $this->assertSame($original->getPort(), $unserialized->getPort());
        $this->assertEquals($original->getTarget(), $unserialized->getTarget());
    }
}
