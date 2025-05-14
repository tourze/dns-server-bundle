<?php

namespace DnsServerBundle\Tests\Service;

use DnsServerBundle\Service\DnsQueryService;
use DnsServerBundle\Service\DnsWorkerService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use React\Datagram\Socket;

class DnsWorkerServiceTest extends TestCase
{
    private DnsWorkerService $service;
    private MockObject $queryService;
    private MockObject $logger;
    
    protected function setUp(): void
    {
        $this->queryService = $this->createMock(DnsQueryService::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        
        $this->service = new DnsWorkerService(
            $this->queryService,
            $this->logger
        );
    }
    
    public function testStart(): void
    {
        $serverIp = '0.0.0.0';
        $port = 53;
        
        // 预期日志调用
        $this->logger->expects($this->once())
            ->method('info')
            ->with($this->stringContains('DNS server listening'));
        
        // 由于我们不能控制Loop::run()，这个测试只能检查日志记录
        // 实际运行可能会导致测试一直挂起
        
        // 创建Socket工厂
        $socketFactory = function () {
            return $this->createMock(Socket::class);
        };
        
        // 模拟启动
        $this->service->start($serverIp, $port, $socketFactory);
    }
    
    public function testHandleMessageCallback(): void
    {
        // 准备测试数据
        $message = 'dummy-dns-message';
        $remoteAddress = '192.168.1.1';
        $socket = $this->createMock(Socket::class);
        
        // 预期查询服务调用
        $this->queryService->expects($this->once())
            ->method('handleQuery')
            ->with($message, $remoteAddress, $socket);
        
        // 提取消息处理回调
        $socket->expects($this->once())
            ->method('on')
            ->with(
                $this->equalTo('message'),
                $this->callback(function ($callback) use ($message, $remoteAddress, $socket) {
                    // 直接调用提取的回调函数
                    $callback($message, $remoteAddress, $socket);
                    return true;
                })
            );
        
        // 模拟启动
        $this->service->start('0.0.0.0', 53, function () use ($socket) {
            return $socket;
        });
    }
    
    public function testHandleMessageCallbackWithError(): void
    {
        // 准备测试数据
        $message = 'dummy-dns-message';
        $remoteAddress = '192.168.1.1';
        $socket = $this->createMock(Socket::class);
        
        // 模拟查询服务抛出异常
        $this->queryService->method('handleQuery')
            ->willThrowException(new \Exception('Query handling error'));
        
        // 预期日志调用
        $this->logger->expects($this->once())
            ->method('error')
            ->with(
                $this->stringContains('Error handling DNS message'),
                $this->callback(function ($context) {
                    return isset($context['error']) && isset($context['remote_address']);
                })
            );
        
        // 提取消息处理回调
        $socket->expects($this->once())
            ->method('on')
            ->with(
                $this->equalTo('message'),
                $this->callback(function ($callback) use ($message, $remoteAddress, $socket) {
                    // 直接调用提取的回调函数
                    $callback($message, $remoteAddress, $socket);
                    return true;
                })
            );
        
        // 模拟启动
        $this->service->start('0.0.0.0', 53, function () use ($socket) {
            return $socket;
        });
    }
} 