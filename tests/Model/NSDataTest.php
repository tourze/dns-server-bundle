<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Model;

use DnsServerBundle\Model\Hostname;
use DnsServerBundle\Model\NSData;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(NSData::class)]
final class NSDataTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Model 测试不需要特别的设置
    }

    public function testConstructorWithHostname(): void
    {
        $hostname = new Hostname('ns1.example.com');
        $nsData = new NSData($hostname);

        $this->assertSame($hostname, $nsData->getTarget());
    }

    public function testToString(): void
    {
        $hostname = new Hostname('ns1.example.com');
        $nsData = new NSData($hostname);

        $this->assertSame('ns1.example.com.', (string) $nsData);
    }

    public function testToArray(): void
    {
        $hostname = new Hostname('ns1.example.com');
        $nsData = new NSData($hostname);

        $expected = [
            'target' => 'ns1.example.com.',
        ];

        $this->assertSame($expected, $nsData->toArray());
    }
}
