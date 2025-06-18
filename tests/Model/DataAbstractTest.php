<?php

namespace DnsServerBundle\Tests\Model;

use DnsServerBundle\Exception\InvalidArgumentDnsServerException;
use DnsServerBundle\Model\CAAData;
use DnsServerBundle\Model\CNAMEData;
use DnsServerBundle\Model\DataAbstract;
use DnsServerBundle\Model\DNSRecordType;
use DnsServerBundle\Model\MXData;
use DnsServerBundle\Model\NSData;
use DnsServerBundle\Model\PTRData;
use DnsServerBundle\Model\SOAData;
use DnsServerBundle\Model\SRVData;
use DnsServerBundle\Model\TXTData;
use PHPUnit\Framework\TestCase;

class DataAbstractTest extends TestCase
{
    public function testCreateFromTypeAndStringWithTXT(): void
    {
        $type = DNSRecordType::createTXT();
        $data = DataAbstract::createFromTypeAndString($type, '"test text"');
        
        $this->assertInstanceOf(TXTData::class, $data);
        $this->assertSame('test text', (string)$data);
    }
    
    public function testCreateFromTypeAndStringWithNS(): void
    {
        $type = DNSRecordType::createNS();
        $data = DataAbstract::createFromTypeAndString($type, 'ns1.example.com');
        
        $this->assertInstanceOf(NSData::class, $data);
        $this->assertStringContainsString('ns1.example.com', (string)$data);
    }
    
    public function testCreateFromTypeAndStringWithCNAME(): void
    {
        $type = DNSRecordType::createCNAME();
        $data = DataAbstract::createFromTypeAndString($type, 'www.example.com');
        
        $this->assertInstanceOf(CNAMEData::class, $data);
        $this->assertStringContainsString('www.example.com', (string)$data);
    }
    
    public function testCreateFromTypeAndStringWithMX(): void
    {
        $type = DNSRecordType::createMX();
        $data = DataAbstract::createFromTypeAndString($type, '10 mail.example.com');
        
        $this->assertInstanceOf(MXData::class, $data);
        $this->assertStringContainsString('mail.example.com', (string)$data);
        $this->assertStringContainsString('10', (string)$data);
    }
    
    public function testCreateFromTypeAndStringWithSOA(): void
    {
        $type = DNSRecordType::createSOA();
        $data = DataAbstract::createFromTypeAndString($type, 'ns1.example.com hostmaster.example.com 2023010101 3600 1800 604800 86400');
        
        $this->assertInstanceOf(SOAData::class, $data);
        $this->assertStringContainsString('ns1.example.com', (string)$data);
        $this->assertStringContainsString('hostmaster.example.com', (string)$data);
    }
    
    public function testCreateFromTypeAndStringWithCAA(): void
    {
        $type = DNSRecordType::createCAA();
        $data = DataAbstract::createFromTypeAndString($type, '0 issue letsencrypt.org');
        
        $this->assertInstanceOf(CAAData::class, $data);
        $this->assertStringContainsString('issue', (string)$data);
        $this->assertStringContainsString('letsencrypt.org', (string)$data);
    }
    
    public function testCreateFromTypeAndStringWithSRV(): void
    {
        $type = DNSRecordType::createSRV();
        $data = DataAbstract::createFromTypeAndString($type, '10 20 5060 sip.example.com');
        
        $this->assertInstanceOf(SRVData::class, $data);
        $this->assertStringContainsString('sip.example.com', (string)$data);
    }
    
    public function testCreateFromTypeAndStringWithPTR(): void
    {
        $type = DNSRecordType::createPTR();
        $data = DataAbstract::createFromTypeAndString($type, 'host.example.com');
        
        $this->assertInstanceOf(PTRData::class, $data);
        $this->assertStringContainsString('host.example.com', (string)$data);
    }
    
    public function testCreateFromTypeAndStringWithInvalidType(): void
    {
        $type = DNSRecordType::createA();
        
        $this->expectException(InvalidArgumentDnsServerException::class);
        DataAbstract::createFromTypeAndString($type, 'invalid data');
    }
    
    public function testEquals(): void
    {
        $type = DNSRecordType::createMX();
        $data1 = DataAbstract::createFromTypeAndString($type, '10 mail.example.com');
        $data2 = DataAbstract::createFromTypeAndString($type, '10 mail.example.com');
        $data3 = DataAbstract::createFromTypeAndString($type, '20 mail.example.com');
        
        $this->assertTrue($data1->equals($data2));
        $this->assertFalse($data1->equals($data3));
    }
    
    public function testJsonSerialize(): void
    {
        $type = DNSRecordType::createMX();
        $data = DataAbstract::createFromTypeAndString($type, '10 mail.example.com');
        
        $json = json_encode($data);
        $decoded = json_decode($json, true);
        $this->assertArrayHasKey('target', $decoded);
        $this->assertArrayHasKey('priority', $decoded);
    }
} 