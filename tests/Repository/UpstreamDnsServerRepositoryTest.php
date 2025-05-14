<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Repository;

use DnsServerBundle\Entity\UpstreamDnsServer;
use DnsServerBundle\Enum\MatchStrategy;
use DnsServerBundle\Repository\UpstreamDnsServerRepository;
use DnsServerBundle\Service\DnsMatcherService;
use PHPUnit\Framework\TestCase;

class UpstreamDnsServerRepositoryTest extends TestCase
{
    protected function setUp(): void
    {
        // 没有实际初始化，只在测试方法中测试方法行为
    }

    public function testFindAllValid_returnsValidServers(): void
    {
        // 创建模拟结果
        $server1 = new UpstreamDnsServer();
        $server1->setName('Server 1')
            ->setHost('8.8.8.8')
            ->setPattern('*')
            ->setStrategy(MatchStrategy::WILDCARD)
            ->setWeight(10);

        $server2 = new UpstreamDnsServer();
        $server2->setName('Server 2')
            ->setHost('1.1.1.1')
            ->setPattern('*')
            ->setStrategy(MatchStrategy::WILDCARD)
            ->setWeight(5);

        $expectedResults = [$server1, $server2];

        // 创建方法测试
        $repository = $this->createPartialMock(
            UpstreamDnsServerRepository::class, 
            ['findAllValid']
        );
        
        $repository->method('findAllValid')
            ->willReturn($expectedResults);

        // 执行并验证结果
        $result = $repository->findAllValid();
        $this->assertSame($expectedResults, $result);
    }

    public function testFindMatchingServer_withMatchingServer_returnsServer(): void
    {
        // 创建模拟服务器
        $server = new UpstreamDnsServer();
        $server->setName('Matching')
            ->setHost('1.1.1.1')
            ->setPattern('*.com')
            ->setStrategy(MatchStrategy::SUFFIX);
            
        // 创建测试域名
        $domain = 'example.com';
        
        // 创建DnsMatcher模拟
        $dnsMatcherService = $this->createMock(DnsMatcherService::class);
        $dnsMatcherService->method('isMatch')
            ->with($domain, $server->getPattern(), $server->getStrategy())
            ->willReturn(true);
            
        // 创建方法测试
        $repository = $this->getMockBuilder(UpstreamDnsServerRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['findAllValid', 'findMatchingServer'])
            ->getMock();
            
        $repository->method('findMatchingServer')
            ->with($domain)
            ->willReturn($server);
            
        // 执行并验证结果
        $result = $repository->findMatchingServer($domain);
        $this->assertSame($server, $result);
    }

    public function testFindMatchingServer_withoutMatchingServer_returnsNull(): void
    {
        // 创建测试域名
        $domain = 'example.com';
        
        // 创建方法测试
        $repository = $this->getMockBuilder(UpstreamDnsServerRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['findMatchingServer'])
            ->getMock();
            
        $repository->method('findMatchingServer')
            ->with($domain)
            ->willReturn(null);
            
        // 执行并验证结果
        $result = $repository->findMatchingServer($domain);
        $this->assertNull($result);
    }

    public function testGetDefaultServer_withDefaultServer_returnsServer(): void
    {
        // 创建期望的结果
        $defaultServer = new UpstreamDnsServer();
        $defaultServer->setName('Default Server')
            ->setHost('8.8.8.8')
            ->setPattern('*')
            ->setStrategy(MatchStrategy::WILDCARD)
            ->setIsDefault(true);

        // 创建方法测试
        $repository = $this->getMockBuilder(UpstreamDnsServerRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getDefaultServer'])
            ->getMock();
            
        $repository->method('getDefaultServer')
            ->willReturn($defaultServer);

        // 执行并验证结果
        $result = $repository->getDefaultServer();
        $this->assertSame($defaultServer, $result);
    }

    public function testGetDefaultServer_withoutDefaultServer_returnsNull(): void
    {
        // 创建方法测试
        $repository = $this->getMockBuilder(UpstreamDnsServerRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getDefaultServer'])
            ->getMock();
            
        $repository->method('getDefaultServer')
            ->willReturn(null);

        // 执行并验证结果
        $result = $repository->getDefaultServer();
        $this->assertNull($result);
    }
} 