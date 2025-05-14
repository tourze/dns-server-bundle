<?php

namespace DnsServerBundle\Tests\Entity;

use DnsServerBundle\Entity\UpstreamDnsServer;
use DnsServerBundle\Enum\DnsProtocolEnum;
use DnsServerBundle\Enum\MatchStrategy;
use PHPUnit\Framework\TestCase;

class UpstreamDnsServerTest extends TestCase
{
    private UpstreamDnsServer $server;
    
    protected function setUp(): void
    {
        $this->server = new UpstreamDnsServer();
        
        // 初始化必要属性以避免null错误
        $this->server->setName('Test Server')
            ->setHost('8.8.8.8')
            ->setPattern('*')
            ->setStrategy(MatchStrategy::WILDCARD);
    }
    
    public function testDefaultValues(): void
    {
        $server = new UpstreamDnsServer();
        $this->assertSame(0, $server->getId());
        $this->assertSame(53, $server->getPort());
        $this->assertSame(5, $server->getTimeout());
        $this->assertSame(1, $server->getWeight());
        $this->assertSame(300, $server->getTtl());
        $this->assertSame(DnsProtocolEnum::UDP, $server->getProtocol());
        $this->assertTrue($server->isVerifyCert());
        $this->assertFalse($server->isDefault());
        $this->assertFalse($server->isValid());
    }
    
    public function testSetGetName(): void
    {
        $name = 'Test Server';
        $this->assertSame($this->server, $this->server->setName($name));
        $this->assertSame($name, $this->server->getName());
    }
    
    public function testSetGetHost(): void
    {
        $host = '8.8.8.8';
        $this->assertSame($this->server, $this->server->setHost($host));
        $this->assertSame($host, $this->server->getHost());
    }
    
    public function testSetGetPort(): void
    {
        $port = 5353;
        $this->assertSame($this->server, $this->server->setPort($port));
        $this->assertSame($port, $this->server->getPort());
    }
    
    public function testSetGetTimeout(): void
    {
        $timeout = 10;
        $this->assertSame($this->server, $this->server->setTimeout($timeout));
        $this->assertSame($timeout, $this->server->getTimeout());
    }
    
    public function testSetGetWeight(): void
    {
        $weight = 5;
        $this->assertSame($this->server, $this->server->setWeight($weight));
        $this->assertSame($weight, $this->server->getWeight());
    }
    
    public function testSetGetDescription(): void
    {
        $description = 'Test description';
        $this->assertSame($this->server, $this->server->setDescription($description));
        $this->assertSame($description, $this->server->getDescription());
    }
    
    public function testSetGetPattern(): void
    {
        $pattern = '*.example.com';
        $this->assertSame($this->server, $this->server->setPattern($pattern));
        $this->assertSame($pattern, $this->server->getPattern());
    }
    
    public function testSetGetStrategy(): void
    {
        $strategy = MatchStrategy::SUFFIX;
        $this->assertSame($this->server, $this->server->setStrategy($strategy));
        $this->assertSame($strategy, $this->server->getStrategy());
    }
    
    public function testSetIsDefault(): void
    {
        $this->assertSame($this->server, $this->server->setIsDefault(true));
        $this->assertTrue($this->server->isDefault());
        
        $this->assertSame($this->server, $this->server->setIsDefault(false));
        $this->assertFalse($this->server->isDefault());
    }
    
    public function testSetGetCustomAnswers(): void
    {
        $answers = ['192.168.1.1', '192.168.1.2'];
        $this->assertSame($this->server, $this->server->setCustomAnswers($answers));
        $this->assertSame($answers, $this->server->getCustomAnswers());
    }
    
    public function testSetGetTtl(): void
    {
        $ttl = 600;
        $this->assertSame($this->server, $this->server->setTtl($ttl));
        $this->assertSame($ttl, $this->server->getTtl());
    }
    
    public function testSetGetProtocol(): void
    {
        $protocol = DnsProtocolEnum::DOH;
        $this->assertSame($this->server, $this->server->setProtocol($protocol));
        $this->assertSame($protocol, $this->server->getProtocol());
    }
    
    public function testSetGetCertPath(): void
    {
        $certPath = '/path/to/cert.pem';
        $this->assertSame($this->server, $this->server->setCertPath($certPath));
        $this->assertSame($certPath, $this->server->getCertPath());
    }
    
    public function testSetGetKeyPath(): void
    {
        $keyPath = '/path/to/key.pem';
        $this->assertSame($this->server, $this->server->setKeyPath($keyPath));
        $this->assertSame($keyPath, $this->server->getKeyPath());
    }
    
    public function testSetVerifyCert(): void
    {
        $this->assertSame($this->server, $this->server->setVerifyCert(false));
        $this->assertFalse($this->server->isVerifyCert());
        
        $this->assertSame($this->server, $this->server->setVerifyCert(true));
        $this->assertTrue($this->server->isVerifyCert());
    }
    
    public function testSetValid(): void
    {
        $this->assertSame($this->server, $this->server->setValid(true));
        $this->assertTrue($this->server->isValid());
        
        $this->assertSame($this->server, $this->server->setValid(false));
        $this->assertFalse($this->server->isValid());
    }
    
    public function testRetrievePlainArray(): void
    {
        $this->server->setName('Test Server')
            ->setHost('8.8.8.8')
            ->setPort(53)
            ->setPattern('*.example.com')
            ->setStrategy(MatchStrategy::SUFFIX);
            
        $array = $this->server->retrievePlainArray();
        
        $this->assertIsArray($array);
        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('host', $array);
        $this->assertArrayHasKey('port', $array);
        $this->assertArrayHasKey('pattern', $array);
        $this->assertArrayHasKey('strategy', $array);
        
        $this->assertSame('Test Server', $array['name']);
        $this->assertSame('8.8.8.8', $array['host']);
        $this->assertSame(53, $array['port']);
        $this->assertSame('*.example.com', $array['pattern']);
        $this->assertSame(MatchStrategy::SUFFIX, $array['strategy']);
    }
    
    public function testRetrieveApiArray(): void
    {
        $array = $this->server->retrieveApiArray();
        $this->assertIsArray($array);
    }
    
    public function testRetrieveAdminArray(): void
    {
        $array = $this->server->retrieveAdminArray();
        $this->assertIsArray($array);
    }
    
    public function testSetGetCreatedBy(): void
    {
        $createdBy = 'admin';
        $this->assertSame($this->server, $this->server->setCreatedBy($createdBy));
        $this->assertSame($createdBy, $this->server->getCreatedBy());
    }
    
    public function testSetGetUpdatedBy(): void
    {
        $updatedBy = 'admin';
        $this->assertSame($this->server, $this->server->setUpdatedBy($updatedBy));
        $this->assertSame($updatedBy, $this->server->getUpdatedBy());
    }
    
    public function testSetGetCreatedFromIp(): void
    {
        $ip = '127.0.0.1';
        $this->assertSame($this->server, $this->server->setCreatedFromIp($ip));
        $this->assertSame($ip, $this->server->getCreatedFromIp());
    }
    
    public function testSetGetUpdatedFromIp(): void
    {
        $ip = '127.0.0.1';
        $this->assertSame($this->server, $this->server->setUpdatedFromIp($ip));
        $this->assertSame($ip, $this->server->getUpdatedFromIp());
    }
} 