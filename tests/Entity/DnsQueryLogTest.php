<?php

namespace DnsServerBundle\Tests\Entity;

use DnsServerBundle\Entity\DnsQueryLog;
use DnsServerBundle\Enum\RecordType;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(DnsQueryLog::class)]
final class DnsQueryLogTest extends AbstractEntityTestCase
{
    /**
     * @return array<string, array{string, mixed}>
     */
    public static function propertiesProvider(): array
    {
        return [
            'domain' => ['domain', 'example.com'],
            'clientIp' => ['clientIp', '192.168.1.1'],
            'queryType' => ['queryType', RecordType::A],
            'responseTime' => ['responseTime', 150],
            'response' => ['response', 'base64encodedresponse'],
            'isHit' => ['isHit', true],
            'isHitFalse' => ['isHit', false],
            'createTime' => ['createTime', new \DateTimeImmutable()],
        ];
    }

    protected function createEntity(): object
    {
        $entity = new DnsQueryLog();
        $entity->setDomain('example.com');
        $entity->setClientIp('192.168.1.1');
        $entity->setQueryType(RecordType::A);
        $entity->setResponseTime(150);
        $entity->setResponse('base64encodedresponse');
        $entity->setIsHit(true);
        $entity->setCreateTime(new \DateTimeImmutable());

        return $entity;
    }

    public function testDefaultValues(): void
    {
        $newLog = new DnsQueryLog();
        $newLog->setDomain('test.com');
        $newLog->setClientIp('192.168.1.1');
        $newLog->setQueryType(RecordType::A);

        $this->assertNull($newLog->getId());
        $this->assertFalse($newLog->isHit());
        $this->assertSame(0, $newLog->getResponseTime());
        $this->assertNull($newLog->getResponse());
        $this->assertNull($newLog->getCreateTime());
    }

    public function testSetGetDomain(): void
    {
        $domain = 'test.example.com';
        $log = new DnsQueryLog();
        $log->setDomain($domain);
        $this->assertSame($domain, $log->getDomain());
    }

    public function testSetGetClientIp(): void
    {
        $ip = '192.168.1.2';
        $log = new DnsQueryLog();
        $log->setClientIp($ip);
        $this->assertSame($ip, $log->getClientIp());
    }

    public function testSetGetQueryType(): void
    {
        $type = RecordType::AAAA;
        $log = new DnsQueryLog();
        $log->setQueryType($type);
        $this->assertSame($type, $log->getQueryType());
    }

    public function testSetGetResponseTime(): void
    {
        $time = 150;
        $log = new DnsQueryLog();
        $log->setResponseTime($time);
        $this->assertSame($time, $log->getResponseTime());
    }

    public function testSetGetResponse(): void
    {
        $response = 'base64encodedresponse';
        $log = new DnsQueryLog();
        $log->setResponse($response);
        $this->assertSame($response, $log->getResponse());
    }

    public function testSetIsHit(): void
    {
        $log = new DnsQueryLog();
        $log->setIsHit(true);
        $this->assertTrue($log->isHit());

        $log->setIsHit(false);
        $this->assertFalse($log->isHit());
    }

    public function testSetGetCreateTime(): void
    {
        $date = new \DateTimeImmutable();
        $log = new DnsQueryLog();
        $log->setCreateTime($date);
        $this->assertSame($date, $log->getCreateTime());
    }

    public function testToPlainArray(): void
    {
        $log = new DnsQueryLog();
        $log->setDomain('example.com');
        $log->setClientIp('192.168.1.1');
        $log->setQueryType(RecordType::A);

        $array = $log->toPlainArray();
        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('domain', $array);
        $this->assertArrayHasKey('queryType', $array);
        $this->assertArrayHasKey('clientIp', $array);
    }

    public function testRetrievePlainArray(): void
    {
        $log = new DnsQueryLog();
        $log->setDomain('example.com');
        $array = $log->retrievePlainArray();
        $this->assertEquals('example.com', $array['domain']);
    }

    public function testRetrieveAdminArray(): void
    {
        $log = new DnsQueryLog();
        $log->setDomain('example.com');
        $array = $log->retrieveAdminArray();
        $this->assertEquals('example.com', $array['domain']);
    }
}
