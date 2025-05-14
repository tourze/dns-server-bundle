<?php

namespace DnsServerBundle\Tests\Service;

use DnsServerBundle\Entity\DnsQueryLog;
use DnsServerBundle\Entity\UpstreamDnsServer;
use DnsServerBundle\Enum\DnsProtocolEnum;
use DnsServerBundle\Enum\MatchStrategy;
use DnsServerBundle\Repository\UpstreamDnsServerRepository;
use DnsServerBundle\Service\DnsQueryService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use React\Datagram\Socket;
use React\Dns\Model\Message;
use React\Dns\Model\Record as DnsRecord;
use React\Dns\Protocol\BinaryDumper;
use React\Dns\Protocol\Parser;
use React\Dns\Query\CoopExecutor;
use React\Dns\Query\Query;
use React\Promise\Deferred;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\CacheItem;
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
        
        $this->upstreamServer = new UpstreamDnsServer();
        $this->upstreamServer->setHost('8.8.8.8')
            ->setPort(53)
            ->setProtocol(DnsProtocolEnum::UDP)
            ->setTimeout(5)
            ->setPattern('.*')
            ->setStrategy(MatchStrategy::REGEX)
            ->setIsDefault(true);
    }
    
    public function testHandleQuery_WithCachedResponse(): void
    {
        // 创建DNS请求消息
        $request = new Message();
        $request->id = 1234;
        $request->questions[] = new DnsRecord('example.com', Message::TYPE_A, Message::CLASS_IN);
        
        $parser = new Parser();
        $dumper = new BinaryDumper();
        $requestBinary = $dumper->toBinary($request);
        
        // 创建缓存项并设置为命中
        $cacheItem = $this->createMock(CacheItem::class);
        $cacheItem->method('isHit')->willReturn(true);
        
        // 创建缓存的响应数据
        $cachedAnswers = [
            new DnsRecord('example.com', Message::TYPE_A, Message::CLASS_IN, 300, '192.168.1.1')
        ];
        
        $cacheItem->method('get')->willReturn([
            'answers' => $cachedAnswers,
            'authority' => [],
            'additional' => [],
            'expires' => time() + 300
        ]);
        
        // 配置缓存Mock
        $this->cache->method('getItem')
            ->with($this->stringContains('dns_query_example.com_1'))
            ->willReturn($cacheItem);
        
        // 准备测试Socket
        $socket = $this->createMock(Socket::class);
        $socket->expects($this->once())
            ->method('send')
            ->with(
                $this->callback(function ($data) use ($request, $parser) {
                    $response = $parser->parseMessage($data);
                    return $response->id === $request->id && !empty($response->answers);
                }),
                '192.168.0.1'
            );
        
        // 确保实体管理器方法被调用
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(DnsQueryLog::class));
        
        $this->entityManager->expects($this->once())
            ->method('flush');
        
        // 执行测试
        $this->service->handleQuery($requestBinary, '192.168.0.1', $socket);
    }
    
    public function testHandleQuery_WithForwardedQuery(): void
    {
        // 创建DNS请求消息
        $request = new Message();
        $request->id = 1234;
        $request->questions[] = new DnsRecord('example.com', Message::TYPE_A, Message::CLASS_IN);
        
        $parser = new Parser();
        $dumper = new BinaryDumper();
        $requestBinary = $dumper->toBinary($request);
        
        // 创建缓存项并设置为未命中
        $cacheItem = $this->createMock(CacheItem::class);
        $cacheItem->method('isHit')->willReturn(false);
        
        // 创建存储缓存的新项
        $newCacheItem = $this->createMock(CacheItem::class);
        $newCacheItem->expects($this->once())
            ->method('set')
            ->with($this->isType('array'));
        
        $newCacheItem->expects($this->once())
            ->method('expiresAfter')
            ->with($this->isType('integer'));
        
        // 配置缓存Mock
        $this->cache->method('getItem')
            ->with($this->stringContains('dns_query_example.com_1'))
            ->willReturnOnConsecutiveCalls($cacheItem, $newCacheItem);
        
        $this->cache->expects($this->once())
            ->method('save')
            ->with($newCacheItem);
        
        // 配置上游服务器仓库
        $this->upstreamDnsServerRepository->method('findMatchingServer')
            ->with('example.com')
            ->willReturn($this->upstreamServer);
        
        // 创建响应消息
        $response = new Message();
        $response->id = 1234;
        $response->qr = true;
        $response->questions[] = new DnsRecord('example.com', Message::TYPE_A, Message::CLASS_IN);
        $response->answers[] = new DnsRecord('example.com', Message::TYPE_A, Message::CLASS_IN, 300, '192.168.1.1');
        
        // 创建Promise和Executor的Mock
        $deferred = new Deferred();
        $promise = $deferred->promise();
        
        $executor = $this->createMock(CoopExecutor::class);
        $executor->method('query')
            ->with($this->callback(function (Query $query) {
                return $query->name === 'example.com' && $query->type === Message::TYPE_A;
            }))
            ->willReturn($promise);
        
        // 使用反射修改服务中的方法，使其返回我们的Mock执行器
        $reflection = new \ReflectionClass(DnsQueryService::class);
        $method = $reflection->getMethod('createExecutor');
        $method->setAccessible(true);
        
        $serviceMock = $this->getMockBuilder(DnsQueryService::class)
            ->setConstructorArgs([
                $this->upstreamDnsServerRepository,
                $this->entityManager,
                $this->logger,
                $this->cache,
                $this->httpClient
            ])
            ->onlyMethods(['createExecutor'])
            ->getMock();
        
        $serviceMock->method('createExecutor')
            ->with($this->upstreamServer)
            ->willReturn($executor);
        
        // 准备测试Socket
        $socket = $this->createMock(Socket::class);
        $socket->expects($this->once())
            ->method('send')
            ->with(
                $this->callback(function ($data) use ($parser) {
                    $msg = $parser->parseMessage($data);
                    return !empty($msg->answers) && $msg->answers[0]->data === '192.168.1.1';
                }),
                '192.168.0.1'
            );
        
        // 确保实体管理器方法被调用
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(DnsQueryLog::class));
        
        $this->entityManager->expects($this->once())
            ->method('flush');
        
        // 执行测试
        $serviceMock->handleQuery($requestBinary, '192.168.0.1', $socket);
        
        // 解析Promise以触发回调
        $deferred->resolve($response);
    }
    
    public function testHandleQuery_WithError(): void
    {
        // 创建DNS请求消息
        $request = new Message();
        $request->id = 1234;
        $request->questions[] = new DnsRecord('example.com', Message::TYPE_A, Message::CLASS_IN);
        
        $parser = new Parser();
        $dumper = new BinaryDumper();
        $requestBinary = $dumper->toBinary($request);
        
        // 创建缓存项并设置为未命中
        $cacheItem = $this->createMock(CacheItem::class);
        $cacheItem->method('isHit')->willReturn(false);
        
        // 配置缓存Mock
        $this->cache->method('getItem')
            ->with($this->stringContains('dns_query_example.com_1'))
            ->willReturn($cacheItem);
        
        // 配置上游服务器仓库
        $this->upstreamDnsServerRepository->method('findMatchingServer')
            ->with('example.com')
            ->willReturn($this->upstreamServer);
        
        // 创建Promise和Executor的Mock
        $deferred = new Deferred();
        $promise = $deferred->promise();
        
        $executor = $this->createMock(CoopExecutor::class);
        $executor->method('query')
            ->willReturn($promise);
        
        // 使用反射修改服务中的方法，使其返回我们的Mock执行器
        $serviceMock = $this->getMockBuilder(DnsQueryService::class)
            ->setConstructorArgs([
                $this->upstreamDnsServerRepository,
                $this->entityManager,
                $this->logger,
                $this->cache,
                $this->httpClient
            ])
            ->onlyMethods(['createExecutor'])
            ->getMock();
        
        $serviceMock->method('createExecutor')
            ->willReturn($executor);
        
        // 准备测试Socket
        $socket = $this->createMock(Socket::class);
        $socket->expects($this->once())
            ->method('send')
            ->with(
                $this->callback(function ($data) use ($parser) {
                    $msg = $parser->parseMessage($data);
                    return $msg->rcode === 2; // SERVFAIL
                }),
                '192.168.0.1'
            );
        
        // 确保实体管理器方法被调用
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(DnsQueryLog::class));
        
        $this->entityManager->expects($this->once())
            ->method('flush');
        
        // 执行测试
        $serviceMock->handleQuery($requestBinary, '192.168.0.1', $socket);
        
        // 解析Promise为错误以触发错误回调
        $deferred->reject(new \Exception('DNS query failed'));
    }
} 