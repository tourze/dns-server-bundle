<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Model;

use DnsServerBundle\Model\Hostname;
use DnsServerBundle\Model\PTRData;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(PTRData::class)]
final class PTRDataTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Model 测试不需要特别的设置
    }

    public function testConstructorWithHostname(): void
    {
        $hostname = new Hostname('example.com');
        $ptrData = new PTRData($hostname);

        $this->assertSame($hostname, $ptrData->getHostname());
    }

    public function testToString(): void
    {
        $hostname = new Hostname('example.com');
        $ptrData = new PTRData($hostname);

        $this->assertSame('example.com.', (string) $ptrData);
    }

    public function testToArray(): void
    {
        $hostname = new Hostname('example.com');
        $ptrData = new PTRData($hostname);

        $expected = [
            'hostname' => 'example.com.',
        ];

        $this->assertSame($expected, $ptrData->toArray());
    }
}
