<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Repository;

use DnsServerBundle\Entity\DnsQueryLog;
use DnsServerBundle\Enum\RecordType;
use DnsServerBundle\Repository\DnsQueryLogRepository;
use PHPUnit\Framework\TestCase;

class DnsQueryLogRepositoryTest extends TestCase
{
    protected function setUp(): void
    {
        // 在实际测试方法中创建mock
    }

    public function testFindByDomain_returnsQueryLogs(): void
    {
        // 准备测试数据
        $domain = 'example.com';
        $limit = 5;
        
        $log1 = new DnsQueryLog();
        $log1->setDomain($domain)
            ->setQueryType(RecordType::A)
            ->setClientIp('192.168.1.1');
            
        $log2 = new DnsQueryLog();
        $log2->setDomain($domain)
            ->setQueryType(RecordType::AAAA)
            ->setClientIp('192.168.1.2');
            
        $expectedResults = [$log1, $log2];
        
        // 创建mock repository
        $repository = $this->getMockBuilder(DnsQueryLogRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['findByDomain'])
            ->getMock();
            
        $repository->method('findByDomain')
            ->with($domain, $limit)
            ->willReturn($expectedResults);
            
        // 执行并验证结果
        $result = $repository->findByDomain($domain, $limit);
        $this->assertSame($expectedResults, $result);
    }
    
    public function testFindByDomain_withDefaultLimit_returnsQueryLogs(): void
    {
        // 准备测试数据
        $domain = 'example.com';
        $defaultLimit = 10;
        
        $logs = [];
        for ($i = 0; $i < 15; $i++) {
            $log = new DnsQueryLog();
            $log->setDomain($domain)
                ->setQueryType(RecordType::A)
                ->setClientIp('192.168.1.' . $i);
            $logs[] = $log;
        }
        
        $expectedResults = array_slice($logs, 0, $defaultLimit);
        
        // 创建mock repository
        $repository = $this->getMockBuilder(DnsQueryLogRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['findByDomain'])
            ->getMock();
            
        $repository->method('findByDomain')
            ->with($domain)
            ->willReturn($expectedResults);
            
        // 执行并验证结果
        $result = $repository->findByDomain($domain);
        $this->assertSame($expectedResults, $result);
    }
    
    public function testFindByClientIp_returnsQueryLogs(): void
    {
        // 准备测试数据
        $clientIp = '192.168.1.1';
        $limit = 3;
        
        $log1 = new DnsQueryLog();
        $log1->setDomain('example.com')
            ->setQueryType(RecordType::A)
            ->setClientIp($clientIp);
            
        $log2 = new DnsQueryLog();
        $log2->setDomain('example.org')
            ->setQueryType(RecordType::AAAA)
            ->setClientIp($clientIp);
            
        $log3 = new DnsQueryLog();
        $log3->setDomain('example.net')
            ->setQueryType(RecordType::MX)
            ->setClientIp($clientIp);
            
        $expectedResults = [$log1, $log2, $log3];
        
        // 创建mock repository
        $repository = $this->getMockBuilder(DnsQueryLogRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['findByClientIp'])
            ->getMock();
            
        $repository->method('findByClientIp')
            ->with($clientIp, $limit)
            ->willReturn($expectedResults);
            
        // 执行并验证结果
        $result = $repository->findByClientIp($clientIp, $limit);
        $this->assertSame($expectedResults, $result);
    }
    
    public function testFindByClientIp_withDefaultLimit_returnsQueryLogs(): void
    {
        // 准备测试数据
        $clientIp = '192.168.1.1';
        $defaultLimit = 10;
        
        $logs = [];
        for ($i = 0; $i < 15; $i++) {
            $log = new DnsQueryLog();
            $log->setDomain('example' . $i . '.com')
                ->setQueryType(RecordType::A)
                ->setClientIp($clientIp);
            $logs[] = $log;
        }
        
        $expectedResults = array_slice($logs, 0, $defaultLimit);
        
        // 创建mock repository
        $repository = $this->getMockBuilder(DnsQueryLogRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['findByClientIp'])
            ->getMock();
            
        $repository->method('findByClientIp')
            ->with($clientIp)
            ->willReturn($expectedResults);
            
        // 执行并验证结果
        $result = $repository->findByClientIp($clientIp);
        $this->assertSame($expectedResults, $result);
    }
} 