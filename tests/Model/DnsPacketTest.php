<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Model;

use DnsServerBundle\Model\DnsPacket;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(DnsPacket::class)]
final class DnsPacketTest extends TestCase
{
    private string $sampleDnsData;

    protected function setUp(): void
    {
        parent::setUp();

        // 创建一个简单的DNS查询数据包 (查询example.com的A记录)
        $this->sampleDnsData = pack(
            'nnnnnn',
            0x1234,  // ID
            0x0100,  // Flags: QR=0, OPCODE=0, AA=0, TC=0, RD=1, RA=0, Z=0, RCODE=0
            1,       // QDCOUNT
            0,       // ANCOUNT
            0,       // NSCOUNT
            0        // ARCOUNT
        );

        // 添加问题部分: example.com A IN
        $this->sampleDnsData .= "\x07example\x03com\x00"; // 域名
        $this->sampleDnsData .= pack('nn', 1, 1); // TYPE=A, CLASS=IN
    }

    public function testConstructorParsesHeader(): void
    {
        $packet = new DnsPacket($this->sampleDnsData);

        $this->assertSame(pack('n', 0x1234), $packet->getId());
        $this->assertFalse($packet->isResponse());
        $this->assertSame(0, $packet->getOpcode());
        $this->assertFalse($packet->isAuthoritative());
        $this->assertFalse($packet->isTruncated());
        $this->assertSame(0, $packet->getRcode());
    }

    public function testGetQuestions(): void
    {
        $packet = new DnsPacket($this->sampleDnsData);
        $questions = $packet->getQuestions();

        $this->assertCount(1, $questions);
        $this->assertSame('example.com', $questions[0]['name']);
        $this->assertSame(1, $questions[0]['type']); // A record
        $this->assertSame(1, $questions[0]['class']); // IN class
    }

    public function testGetEmptyArraysForNewPacket(): void
    {
        $packet = new DnsPacket($this->sampleDnsData);

        $this->assertEmpty($packet->getAnswers());
        $this->assertEmpty($packet->getAuthorities());
        $this->assertEmpty($packet->getAdditionals());
    }

    public function testBuildResponse(): void
    {
        $packet = new DnsPacket($this->sampleDnsData);

        $answers = [
            [
                'name' => 'example.com',
                'type' => 1,
                'class' => 1,
                'ttl' => 300,
                'data' => pack('N', ip2long('192.0.2.1')),
            ],
        ];

        $response = $packet->buildResponse($answers);

        $this->assertNotEmpty($response);
        $this->assertGreaterThan(strlen($this->sampleDnsData), strlen($response));

        // 验证响应包的基本结构
        $header = unpack('nid/nflags/nqdcount/nancount/nnscount/narcount', substr($response, 0, 12));
        $this->assertNotFalse($header);
        $this->assertIsArray($header);
        $this->assertArrayHasKey('flags', $header);
        $flags = $header['flags'];
        $this->assertIsInt($flags);

        $this->assertSame(0x1234, $header['id']);
        $this->assertSame(1, $header['qdcount']);
        $this->assertSame(1, $header['ancount']);
        $this->assertNotSame(0, $flags & 0x8000); // 检查QR位是否设置为1
    }
}
