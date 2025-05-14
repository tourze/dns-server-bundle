<?php

namespace DnsServerBundle\Tests\Service;

use DnsServerBundle\Entity\DnsQueryLog;
use DnsServerBundle\Entity\UpstreamDnsServer;
use DnsServerBundle\Enum\DnsProtocolEnum;
use DnsServerBundle\Enum\MatchStrategy;
use DnsServerBundle\Enum\RecordType;
use DnsServerBundle\Repository\UpstreamDnsServerRepository;
use DnsServerBundle\Service\DnsQueryService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use React\Datagram\Socket;
use React\Dns\Model\Message;
use React\Dns\Model\Record as DnsRecord;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DnsQueryServiceTest extends TestCase
{
    private DnsQueryService $service;
    private MockObject $upstreamDnsServerRepository;
    private MockObject $entityManager;
    private MockObject $logger;
    private MockObject $cache;
    private MockObject $httpClient;
    private UpstreamDnsServer $upstreamServer;
    
    protected function setUp(): void
    {
        $this->upstreamDnsServerRepository = $this->createMock(UpstreamDnsServerRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->cache = $this->createMock(AdapterInterface::class);
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        
        $this->service = new DnsQueryService(
            $this->upstreamDnsServerRepository,
            $this->entityManager,
            $this->logger,
            $this->cache,
            $this->httpClient
        );
        
        // 创建上游服务器实例
        $this->upstreamServer = new UpstreamDnsServer();
        $this->upstreamServer->setName('Google DNS')
            ->setHost('8.8.8.8')
            ->setPort(53)
            ->setPattern('*')
            ->setStrategy(MatchStrategy::WILDCARD)
            ->setProtocol(DnsProtocolEnum::UDP)
            ->setIsDefault(true);
    }
    
    /**
     * 测试日志记录功能
     */
    public function testLogDnsQuery(): void
    {
        // 创建模拟对象
        $repository = $this->createMock(UpstreamDnsServerRepository::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $cache = $this->createMock(AdapterInterface::class);
        $httpClient = $this->createMock(HttpClientInterface::class);
        
        // 创建测试服务
        $service = new DnsQueryService(
            $repository,
            $entityManager,
            $logger,
            $cache,
            $httpClient
        );
        
        // 使用反射访问私有方法
        $reflectionClass = new \ReflectionClass(DnsQueryService::class);
        $method = $reflectionClass->getMethod('createQueryLog');
        $method->setAccessible(true);
        
        // 测试创建日志对象
        $queryLog = $method->invokeArgs($service, ['example.com', Message::TYPE_A, '192.168.1.1']);
        
        // 验证日志对象
        $this->assertInstanceOf(DnsQueryLog::class, $queryLog);
        $this->assertEquals('example.com', $queryLog->getDomain());
        $this->assertEquals(RecordType::A, $queryLog->getQueryType());
        $this->assertEquals('192.168.1.1', $queryLog->getClientIp());
    }
    
    /**
     * 测试缓存功能
     */
    public function testGetCachedResponse(): void
    {
        // 创建模拟对象
        $repository = $this->createMock(UpstreamDnsServerRepository::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $cache = $this->createMock(AdapterInterface::class);
        $httpClient = $this->createMock(HttpClientInterface::class);
        
        // 创建测试服务
        $service = new DnsQueryService(
            $repository,
            $entityManager,
            $logger,
            $cache,
            $httpClient
        );
        
        // 反射访问私有的缓存键方法
        $reflectionClass = new \ReflectionClass(DnsQueryService::class);
        $cacheKeyMethod = $reflectionClass->getMethod('getCacheKey');
        $cacheKeyMethod->setAccessible(true);
        
        // 测试缓存键格式
        $cacheKey = $cacheKeyMethod->invokeArgs($service, ['example.com', Message::TYPE_A]);
        $this->assertStringContainsString('example.com', $cacheKey);
        $this->assertStringContainsString('dns_query', $cacheKey);
    }
    
    /**
     * 测试成功处理查询
     */
    public function testHandleQuerySuccess(): void
    {
        // 创建模拟对象
        $repository = $this->createMock(UpstreamDnsServerRepository::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $cache = $this->createMock(AdapterInterface::class);
        $httpClient = $this->createMock(HttpClientInterface::class);
        
        // 设置实体管理器期望
        $entityManager->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(DnsQueryLog::class));
            
        $entityManager->expects($this->once())
            ->method('flush');
        
        // 创建消息和日志
        $request = new Message();
        $request->id = 1234;
        
        $response = new Message();
        $response->answers[] = new DnsRecord('example.com', Message::TYPE_A, Message::CLASS_IN, 300, '192.168.1.1');
        
        $queryLog = new DnsQueryLog();
        $queryLog->setDomain('example.com')
            ->setQueryType(RecordType::A)
            ->setClientIp('192.168.1.1');
        
        $socket = $this->createMock(Socket::class);
        $socket->expects($this->once())
            ->method('send')
            ->with($this->anything(), '192.168.1.1');
        
        // 创建测试服务
        $service = new DnsQueryService(
            $repository,
            $entityManager,
            $logger,
            $cache,
            $httpClient
        );
        
        // 使用反射访问私有方法
        $reflectionClass = new \ReflectionClass(DnsQueryService::class);
        $method = $reflectionClass->getMethod('handleQuerySuccess');
        $method->setAccessible(true);
        
        // 测试处理查询成功
        $method->invokeArgs($service, [
            $response,
            $socket,
            '192.168.1.1',
            $request,
            $queryLog,
            microtime(true)
        ]);
    }
    
    /**
     * 测试失败处理查询
     */
    public function testHandleQueryFailure(): void
    {
        // 创建模拟对象
        $repository = $this->createMock(UpstreamDnsServerRepository::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $cache = $this->createMock(AdapterInterface::class);
        $httpClient = $this->createMock(HttpClientInterface::class);
        
        // 设置实体管理器期望
        $entityManager->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(DnsQueryLog::class));
            
        $entityManager->expects($this->once())
            ->method('flush');
        
        // 创建消息、错误和日志
        $request = new Message();
        $request->id = 1234;
        
        $error = new \Exception('DNS lookup failed');
        
        $queryLog = new DnsQueryLog();
        $queryLog->setDomain('example.com')
            ->setQueryType(RecordType::A)
            ->setClientIp('192.168.1.1');
        
        $socket = $this->createMock(Socket::class);
        $socket->expects($this->once())
            ->method('send')
            ->with($this->anything(), '192.168.1.1');
        
        // 创建测试服务
        $service = new DnsQueryService(
            $repository,
            $entityManager,
            $logger,
            $cache,
            $httpClient
        );
        
        // 使用反射访问私有方法
        $reflectionClass = new \ReflectionClass(DnsQueryService::class);
        $method = $reflectionClass->getMethod('handleQueryFailure');
        $method->setAccessible(true);
        
        // 测试处理查询失败
        $method->invokeArgs($service, [
            $error,
            $socket,
            '192.168.1.1',
            $request,
            $queryLog,
            microtime(true)
        ]);
    }
} 