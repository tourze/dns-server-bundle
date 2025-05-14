<?php

namespace DnsServerBundle\Tests\Service;

use DnsServerBundle\Entity\UpstreamDnsServer;
use DnsServerBundle\Enum\DnsProtocolEnum;
use DnsServerBundle\Service\DnsResolver;
use PHPUnit\Framework\TestCase;
use React\Dns\Model\Message;

class DnsResolverTest extends TestCase
{
    private DnsResolver $resolver;
    private UpstreamDnsServer $server;
    
    protected function setUp(): void
    {
        $this->resolver = new DnsResolver();
        
        $this->server = new UpstreamDnsServer();
        $this->server->setHost('8.8.8.8')
            ->setPort(53)
            ->setProtocol(DnsProtocolEnum::UDP)
            ->setTimeout(5);
    }
    
    public function testCreateCustomResponse_WithSingleIpv4(): void
    {
        $domain = 'example.com';
        $ips = ['192.168.1.1'];
        $ttl = 300;
        
        $response = $this->resolver->createCustomResponse($domain, $ips, $ttl);
        
        $this->assertInstanceOf(Message::class, $response);
        $this->assertTrue($response->qr);
        $this->assertTrue($response->rd);
        $this->assertTrue($response->ra);
        $this->assertSame(Message::RCODE_OK, $response->rcode);
        
        $this->assertCount(1, $response->questions);
        $this->assertCount(1, $response->answers);
        
        $question = $response->questions[0];
        $this->assertSame($domain, $question->name);
        $this->assertSame(Message::TYPE_A, $question->type);
        $this->assertSame(Message::CLASS_IN, $question->class);
        
        $answer = $response->answers[0];
        $this->assertSame($domain, $answer->name);
        $this->assertSame(Message::TYPE_A, $answer->type);
        $this->assertSame(Message::CLASS_IN, $answer->class);
        $this->assertSame($ttl, $answer->ttl);
        $this->assertSame($ips[0], $answer->data);
    }
    
    public function testCreateCustomResponse_WithMultipleIpv4(): void
    {
        $domain = 'example.com';
        $ips = ['192.168.1.1', '192.168.1.2', '192.168.1.3'];
        $ttl = 300;
        
        $response = $this->resolver->createCustomResponse($domain, $ips, $ttl);
        
        $this->assertInstanceOf(Message::class, $response);
        $this->assertCount(1, $response->questions);
        $this->assertCount(3, $response->answers);
        
        foreach ($ips as $index => $ip) {
            $answer = $response->answers[$index];
            $this->assertSame($domain, $answer->name);
            $this->assertSame(Message::TYPE_A, $answer->type);
            $this->assertSame($ttl, $answer->ttl);
            $this->assertSame($ip, $answer->data);
        }
    }
    
    public function testCreateCustomResponse_WithIpv6(): void
    {
        $domain = 'example.com';
        $ips = ['2001:0db8:85a3:0000:0000:8a2e:0370:7334'];
        $ttl = 300;
        
        $response = $this->resolver->createCustomResponse($domain, $ips, $ttl, true);
        
        $this->assertInstanceOf(Message::class, $response);
        $this->assertCount(1, $response->questions);
        $this->assertCount(1, $response->answers);
        
        $question = $response->questions[0];
        $this->assertSame(Message::TYPE_AAAA, $question->type);
        
        $answer = $response->answers[0];
        $this->assertSame(Message::TYPE_AAAA, $answer->type);
        $this->assertSame($ips[0], $answer->data);
    }
    
    public function testCreateCustomResponse_WithZeroRecords(): void
    {
        $domain = 'example.com';
        $ips = [];
        $ttl = 300;
        
        $response = $this->resolver->createCustomResponse($domain, $ips, $ttl);
        
        $this->assertInstanceOf(Message::class, $response);
        $this->assertCount(1, $response->questions);
        $this->assertCount(0, $response->answers);
    }
    
    public function testQuery_MethodExists(): void
    {
        $this->assertTrue(method_exists($this->resolver, 'query'));
    }
    
    public function testQueryIpv6_MethodExists(): void
    {
        $this->assertTrue(method_exists($this->resolver, 'queryIpv6'));
    }
} 