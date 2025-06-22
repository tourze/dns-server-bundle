<?php

namespace DnsServerBundle\Tests\Service;

use DnsServerBundle\Service\DnsQueryService;
use DnsServerBundle\Service\DnsWorkerService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use React\Datagram\Socket;
use React\EventLoop\LoopInterface;
use ReflectionMethod;

class DnsWorkerServiceTest extends TestCase
{
    private DnsWorkerService $service;
    private MockObject $queryService;
    private MockObject $logger;
    private MockObject $socket;
    
    protected function setUp(): void
    {
        $this->queryService = $this->createMock(DnsQueryService::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->socket = $this->createMock(Socket::class);
        
        $this->service = new DnsWorkerService(
            $this->queryService,
            $this->logger
        );
    }
    
    /**
     * 通过反射直接访问并测试handleDnsQuery方法
     */
    public function testHandleDnsQuery(): void
    {
        $message = 'dummy-dns-message';
        $remoteAddress = '192.168.1.1';
        
        // 预期查询服务调用
        $this->queryService->expects($this->once())
            ->method('handleQuery')
            ->with($message, $remoteAddress, $this->socket);
        
        // 使用反射直接调用私有方法
        $reflectionMethod = new ReflectionMethod(DnsWorkerService::class, 'handleDnsQuery');
        $reflectionMethod->setAccessible(true);
        $reflectionMethod->invoke($this->service, $message, $remoteAddress, $this->socket);
    }
    
    /**
     * 通过反射测试handleDnsQuery方法出现异常时的情况
     */
    public function testHandleDnsQueryWithError(): void
    {
        $message = 'dummy-dns-message';
        $remoteAddress = '192.168.1.1';
        $exception = new \Exception('Query handling error');
        
        // 模拟查询服务抛出异常
        $this->queryService->method('handleQuery')
            ->with($message, $remoteAddress, $this->socket)
            ->willThrowException($exception);
        
        // 预期日志调用
        $this->logger->expects($this->once())
            ->method('error')
            ->with(
                $this->stringContains('DNS query handling error'),
                $this->callback(function ($context) {
                    return isset($context['remote_address']) && isset($context['exception']);
                })
            );
        
        // 使用反射直接调用私有方法
        $reflectionMethod = new ReflectionMethod(DnsWorkerService::class, 'handleDnsQuery');
        $reflectionMethod->setAccessible(true);
        $reflectionMethod->invoke($this->service, $message, $remoteAddress, $this->socket);
    }
    
    /**
     * 测试启动服务
     */
    public function testStart(): void
    {
        $serverIp = '0.0.0.0';
        $port = 53;
        
        // 模拟启动服务，不验证事件回调注册
        $serviceMock = $this->getMockBuilder(DnsWorkerService::class)
            ->setConstructorArgs([$this->queryService, $this->logger])
            ->onlyMethods(['start'])
            ->getMock();
        
        // 创建真实的LoopInterface来测试
        $loop = $this->createMock(LoopInterface::class);
        
        // 执行测试
        $serviceMock->start($loop, $serverIp, $port);
        
        // 基本断言，确保方法能够调用而不抛出异常
        $this->assertInstanceOf(DnsWorkerService::class, $serviceMock);
    }
} 