<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Integration\Repository;

use DnsServerBundle\Entity\DnsQueryLog;
use DnsServerBundle\Repository\DnsQueryLogRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\TestCase;

class DnsQueryLogRepositoryTest extends TestCase
{
    public function testRepositoryCanBeInstantiated(): void
    {
        $registry = $this->createMock(\Doctrine\Persistence\ManagerRegistry::class);
        $repository = new DnsQueryLogRepository($registry);
        
        $this->assertInstanceOf(DnsQueryLogRepository::class, $repository);
        $this->assertInstanceOf(ServiceEntityRepository::class, $repository);
    }

    public function testRepositoryHasCustomMethods(): void
    {
        $registry = $this->createMock(\Doctrine\Persistence\ManagerRegistry::class);
        $repository = new DnsQueryLogRepository($registry);
        
        // 验证仓储有自定义方法
        $reflection = new \ReflectionClass($repository);
        $this->assertTrue($reflection->hasMethod('findByDomain'));
        $this->assertTrue($reflection->hasMethod('findByClientIp'));
    }
}