<?php

namespace DnsServerBundle\Tests\Entity;

use DnsServerBundle\Entity\DnsQueryLog;
use DnsServerBundle\Enum\RecordType;
use PHPUnit\Framework\TestCase;

class DnsQueryLogTest extends TestCase
{
    private DnsQueryLog $log;
    
    protected function setUp(): void
    {
        $this->log = new DnsQueryLog();
        
        // 初始化必需字段
        $this->log->setDomain('example.com')
            ->setClientIp('192.168.1.1')
            ->setQueryType(RecordType::A);
    }
    
    public function testDefaultValues(): void
    {
        $newLog = new DnsQueryLog();
        $newLog->setDomain('test.com')
            ->setClientIp('192.168.1.1')
            ->setQueryType(RecordType::A);
        
        $this->assertSame(0, $newLog->getId());
        $this->assertFalse($newLog->isHit());
        $this->assertSame(0, $newLog->getResponseTime());
        $this->assertNull($newLog->getResponse());
        $this->assertNull($newLog->getCreateTime());
    }
    
    public function testSetGetDomain(): void
    {
        $domain = 'test.example.com';
        $this->assertSame($this->log, $this->log->setDomain($domain));
        $this->assertSame($domain, $this->log->getDomain());
    }
    
    public function testSetGetClientIp(): void
    {
        $ip = '192.168.1.2';
        $this->assertSame($this->log, $this->log->setClientIp($ip));
        $this->assertSame($ip, $this->log->getClientIp());
    }
    
    public function testSetGetQueryType(): void
    {
        $type = RecordType::AAAA;
        $this->assertSame($this->log, $this->log->setQueryType($type));
        $this->assertSame($type, $this->log->getQueryType());
    }
    
    public function testSetGetResponseTime(): void
    {
        $time = 150;
        $this->assertSame($this->log, $this->log->setResponseTime($time));
        $this->assertSame($time, $this->log->getResponseTime());
    }
    
    public function testSetGetResponse(): void
    {
        $response = 'base64encodedresponse';
        $this->assertSame($this->log, $this->log->setResponse($response));
        $this->assertSame($response, $this->log->getResponse());
    }
    
    public function testSetIsHit(): void
    {
        $this->assertSame($this->log, $this->log->setIsHit(true));
        $this->assertTrue($this->log->isHit());
        
        $this->assertSame($this->log, $this->log->setIsHit(false));
        $this->assertFalse($this->log->isHit());
    }
    
    public function testSetGetCreateTime(): void
    {
        $date = new \DateTime();
        $this->assertSame($this->log, $this->log->setCreateTime($date));
        $this->assertSame($date, $this->log->getCreateTime());
    }
    
    public function testToPlainArray(): void
    {
        $array = $this->log->toPlainArray();
        
        $this->assertIsArray($array);
        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('domain', $array);
        $this->assertArrayHasKey('queryType', $array);
        $this->assertArrayHasKey('clientIp', $array);
    }
    
    public function testRetrievePlainArray(): void
    {
        $this->log->setDomain('example.com');
        $array = $this->log->retrievePlainArray();
        $this->assertIsArray($array);
    }
    
    public function testRetrieveAdminArray(): void
    {
        $this->log->setDomain('example.com');
        $array = $this->log->retrieveAdminArray();
        $this->assertIsArray($array);
    }
} 