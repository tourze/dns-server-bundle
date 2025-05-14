<?php

namespace DnsServerBundle\Tests\Service;

use DnsServerBundle\Entity\UpstreamDnsServer;
use DnsServerBundle\Enum\DnsProtocolEnum;
use DnsServerBundle\Exception\QueryFailure;
use DnsServerBundle\Service\DnsOverHttpsExecutor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use React\Dns\Model\Message;
use React\Dns\Protocol\BinaryDumper;
use React\Dns\Query\Query;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class DnsOverHttpsExecutorTest extends TestCase
{
    private DnsOverHttpsExecutor $executor;
    private MockObject $httpClient;
    private UpstreamDnsServer $server;
    private BinaryDumper $dumper;
    
    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->dumper = new BinaryDumper();
        
        $this->server = new UpstreamDnsServer();
        $this->server->setHost('dns.google')
            ->setPort(443)
            ->setProtocol(DnsProtocolEnum::DOH)
            ->setTimeout(5);
        
        $this->executor = new DnsOverHttpsExecutor(
            $this->httpClient,
            $this->server,
            $this->dumper
        );
    }
    
    /**
     * 测试GET请求抛出异常的情况
     */
    public function testQuery_WithGet(): void
    {
        // 使用3个参数创建Query，添加Message::CLASS_IN作为第三个参数
        $query = new Query('example.com', Message::TYPE_A, Message::CLASS_IN);
        
        // 模拟HTTP客户端抛出异常
        $exception = new \Exception('Connection failed');
        $this->httpClient->method('request')
            ->willThrowException($exception);
        
        // 执行查询并使用done方式处理Promise
        $promise = $this->executor->query($query);
        
        // 使用done方法处理完成和错误
        $hasError = false;
        $promise->then(
            function () {
                // 不应该执行到这里
                $this->fail('Promise should not resolve successfully');
            },
            function (\Throwable $error) use (&$hasError) {
                $hasError = true;
                $this->assertInstanceOf(QueryFailure::class, $error);
            }
        );
        
        // 必须断言错误被触发
        $this->assertTrue($hasError, '查询应该触发错误回调');
    }
    
    /**
     * 测试POST请求处理非200状态码的情况
     */
    public function testQuery_WithPost(): void
    {
        // 使用3个参数创建Query
        $query = new Query('example.com', Message::TYPE_A, Message::CLASS_IN);
        
        // 创建Mock响应，返回非200状态码
        $httpResponse = $this->createMock(ResponseInterface::class);
        $httpResponse->method('getStatusCode')->willReturn(500);
        
        // 设置HTTP客户端的行为
        $this->httpClient->method('request')
            ->willReturn($httpResponse);
        
        // 模拟getContent抛出异常
        $httpResponse->method('getContent')
            ->willThrowException(new \Exception('Server error'));
        
        // 执行查询并使用done方式处理Promise
        $promise = $this->executor->query($query);
        
        // 使用done方法处理完成和错误
        $hasError = false;
        $promise->then(
            function () {
                // 不应该执行到这里
                $this->fail('Promise should not resolve successfully');
            },
            function (\Throwable $error) use (&$hasError) {
                $hasError = true;
                $this->assertInstanceOf(QueryFailure::class, $error);
            }
        );
        
        // 必须断言错误被触发
        $this->assertTrue($hasError, '查询应该触发错误回调');
    }
    
    /**
     * 测试HTTP错误场景
     */
    public function testQuery_WithHttpError(): void
    {
        // 使用3个参数创建Query
        $query = new Query('example.com', Message::TYPE_A, Message::CLASS_IN);
        
        // 模拟HTTP客户端请求并抛出异常
        $exception = new \Exception('HTTP request failed');
        $this->httpClient->method('request')
            ->willThrowException($exception);
        
        // 执行查询并使用done方式处理Promise
        $promise = $this->executor->query($query);
        
        // 使用done方法处理完成和错误
        $hasError = false;
        $promise->then(
            function () {
                // 不应该执行到这里
                $this->fail('Promise should not resolve successfully');
            },
            function (\Throwable $error) use (&$hasError) {
                $hasError = true;
                $this->assertInstanceOf(QueryFailure::class, $error);
            }
        );
        
        // 必须断言错误被触发
        $this->assertTrue($hasError, '查询应该触发错误回调');
    }
} 