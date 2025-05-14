<?php

namespace DnsServerBundle\Tests\Model;

use DnsServerBundle\Model\Hostname;
use DnsServerBundle\Model\MXData;
use PHPUnit\Framework\TestCase;

class MXDataTest extends TestCase
{
    public function testConstructor(): void
    {
        $hostname = new Hostname('mail.example.com');
        $priority = 10;
        
        $mxData = new MXData($hostname, $priority);
        
        $this->assertSame($hostname, $mxData->getTarget());
        $this->assertSame($priority, $mxData->getPriority());
    }
    
    public function testToString(): void
    {
        $hostname = new Hostname('mail.example.com');
        $priority = 10;
        
        $mxData = new MXData($hostname, $priority);
        
        $this->assertStringContainsString((string)$hostname, (string)$mxData);
        $this->assertStringContainsString((string)$priority, (string)$mxData);
    }
    
    public function testToArray(): void
    {
        $hostname = new Hostname('mail.example.com');
        $priority = 10;
        
        $mxData = new MXData($hostname, $priority);
        $array = $mxData->toArray();
        
        $this->assertIsArray($array);
        $this->assertArrayHasKey('target', $array);
        $this->assertArrayHasKey('priority', $array);
        $this->assertSame((string)$hostname, $array['target']);
        $this->assertSame($priority, $array['priority']);
    }
    
    public function testEquals(): void
    {
        $hostname1 = new Hostname('mail.example.com');
        $hostname2 = new Hostname('mail2.example.com');
        
        $mxData1 = new MXData($hostname1, 10);
        $mxData2 = new MXData($hostname1, 10);
        $mxData3 = new MXData($hostname1, 20);
        $mxData4 = new MXData($hostname2, 10);
        
        $this->assertTrue($mxData1->equals($mxData2));
        $this->assertFalse($mxData1->equals($mxData3));
        $this->assertFalse($mxData1->equals($mxData4));
    }
    
    public function testJsonSerialize(): void
    {
        $hostname = new Hostname('mail.example.com');
        $priority = 10;
        
        $mxData = new MXData($hostname, $priority);
        
        $json = json_encode($mxData);
        $decoded = json_decode($json, true);
        
        $this->assertIsArray($decoded);
        $this->assertArrayHasKey('target', $decoded);
        $this->assertArrayHasKey('priority', $decoded);
        $this->assertSame((string)$hostname, $decoded['target']);
        $this->assertSame($priority, $decoded['priority']);
    }
    
    public function testGetTarget(): void
    {
        $hostname = new Hostname('mail.example.com');
        $mxData = new MXData($hostname, 10);
        
        $this->assertSame($hostname, $mxData->getTarget());
    }
    
    public function testGetPriority(): void
    {
        $hostname = new Hostname('mail.example.com');
        $priority = 10;
        
        $mxData = new MXData($hostname, $priority);
        
        $this->assertSame($priority, $mxData->getPriority());
    }
} 