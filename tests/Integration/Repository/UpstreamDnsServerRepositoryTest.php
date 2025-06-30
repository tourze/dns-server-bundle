<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Integration\Repository;

use DnsServerBundle\Entity\UpstreamDnsServer;
use DnsServerBundle\Repository\UpstreamDnsServerRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\TestCase;

class UpstreamDnsServerRepositoryTest extends TestCase
{
    public function testRepositoryCanBeInstantiated(): void
    {
        $registry = $this->createMock(\Doctrine\Persistence\ManagerRegistry::class);
        $matcher = $this->createMock(\DnsServerBundle\Service\DnsMatcherService::class);
        $repository = new UpstreamDnsServerRepository($registry, $matcher);
        
        $this->assertInstanceOf(UpstreamDnsServerRepository::class, $repository);
        $this->assertInstanceOf(ServiceEntityRepository::class, $repository);
    }

    public function testRepositoryHasCustomMethods(): void
    {
        $registry = $this->createMock(\Doctrine\Persistence\ManagerRegistry::class);
        $matcher = $this->createMock(\DnsServerBundle\Service\DnsMatcherService::class);
        $repository = new UpstreamDnsServerRepository($registry, $matcher);
        
        // 验证仓储有自定义方法
        $reflection = new \ReflectionClass($repository);
        $this->assertTrue($reflection->hasMethod('findAllValid'));
        $this->assertTrue($reflection->hasMethod('findMatchingServer'));
        $this->assertTrue($reflection->hasMethod('getDefaultServer'));
    }
}