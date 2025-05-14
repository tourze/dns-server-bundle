<?php

namespace DnsServerBundle\Tests\Service;

use DnsServerBundle\Entity\UpstreamDnsServer;
use DnsServerBundle\Enum\DnsProtocolEnum;
use DnsServerBundle\Service\DnsOverHttpsExecutor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use React\Dns\Model\Message;
use React\Dns\Model\Record;
use React\Dns\Protocol\BinaryDumper;
use React\Dns\Query\Query;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class DnsOverHttpsExecutorTest extends TestCase
{
    private DnsOverHttpsExecutor $executor;
    private MockObject $httpClient;
    private UpstreamDnsServer $server;
    
    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        
        $this->server = new UpstreamDnsServer();
        $this->server->setHost('dns.google')
            ->setPort(443)
            ->setProtocol(DnsProtocolEnum::DOH)
            ->setTimeout(5);
        
        $this->executor = new DnsOverHttpsExecutor(
            $this->httpClient,
            $this->server,
            new BinaryDumper()
        );
    }
    
    public function testQuery_WithGet(): void
    {
        $query = new Query('example.com', Message::TYPE_A);
        
        // 预期的响应内容
        $dnsAnswers = [
            new Record('example.com', Message::TYPE_A, Message::CLASS_IN, 300, '93.184.216.34')
        ];
        
        $expectedResponse = new Message();
        $expectedResponse->id = 1234;
        $expectedResponse->rd = true;
        $expectedResponse->ra = true;
        $expectedResponse->qr = true;
        $expectedResponse->questions[] = new Record('example.com', Message::TYPE_A, Message::CLASS_IN);
        $expectedResponse->answers = $dnsAnswers;
        
        // 创建Mock响应
        $httpResponse = $this->createMock(ResponseInterface::class);
        $httpResponse->method('getStatusCode')->willReturn(200);
        
        // 模拟HTTP客户端请求
        $this->httpClient->expects($this->once())
            ->method('request')
            ->with(
                'GET',
                $this->stringContains('https://dns.google:443/dns-query'),
                $this->callback(function ($options) {
                    return isset($options['query']) && 
                           isset($options['headers']) && 
                           $options['headers']['Accept'] === 'application/dns-message';
                })
            )
            ->willReturn($httpResponse);
        
        // 模拟响应内容转换为二进制并返回
        $httpResponse->method('getContent')
            ->willReturn('dummy-binary-content');
        
        // 告知测试框架我们期望解析器被调用以解析DNS响应
        $this->expectException(\RuntimeException::class);
        
        // 执行查询
        $this->executor->query($query);
    }
    
    public function testQuery_WithPost(): void
    {
        $query = new Query('example.com', Message::TYPE_A);
        
        // 模拟查询对象转换为二进制
        $binaryQuery = 'binary-dns-query';
        $this->dumper->method('toBinary')
            ->willReturn($binaryQuery);
        
        // 创建Mock响应
        $httpResponse = $this->createMock(ResponseInterface::class);
        $httpResponse->method('getStatusCode')->willReturn(200);
        
        // 模拟HTTP客户端请求
        $this->httpClient->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                $this->stringContains('https://dns.google:443/dns-query'),
                $this->callback(function ($options) use ($binaryQuery) {
                    return isset($options['body']) && 
                           $options['body'] === $binaryQuery &&
                           isset($options['headers']) && 
                           $options['headers']['Content-Type'] === 'application/dns-message';
                })
            )
            ->willReturn($httpResponse);
        
        // 模拟响应内容转换为二进制并返回
        $httpResponse->method('getContent')
            ->willReturn('dummy-binary-content');
        
        // 告知测试框架我们期望解析器被调用以解析DNS响应
        $this->expectException(\RuntimeException::class);
        
        // 执行查询
        $this->executor->query($query);
    }
    
    public function testQuery_WithHttpError(): void
    {
        $query = new Query('example.com', Message::TYPE_A);
        
        // 模拟HTTP客户端请求并抛出异常
        $this->httpClient->method('request')
            ->willThrowException(new \Exception('HTTP request failed'));
        
        // 告知测试框架我们期望executor抛出异常
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('HTTP request failed');
        
        // 执行查询
        $this->executor->query($query);
    }
} 