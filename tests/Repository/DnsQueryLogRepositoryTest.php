<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Repository;

use DnsServerBundle\Entity\DnsQueryLog;
use DnsServerBundle\Enum\RecordType;
use DnsServerBundle\Repository\DnsQueryLogRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(DnsQueryLogRepository::class)]
#[RunTestsInSeparateProcesses]
final class DnsQueryLogRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
        // Repository 测试设置
    }

    public function testEntityCanBeCreated(): void
    {
        $entity = new DnsQueryLog();
        $entity->setDomain('example.com');
        $entity->setClientIp('192.168.1.1');
        $entity->setQueryType(RecordType::A);
        $entity->setResponse('127.0.0.1');

        $this->assertSame('example.com', $entity->getDomain());
        $this->assertSame('192.168.1.1', $entity->getClientIp());
        $this->assertSame(RecordType::A, $entity->getQueryType());
        $this->assertSame('127.0.0.1', $entity->getResponse());
    }

    public function testEntityCanSetAllProperties(): void
    {
        $entity = new DnsQueryLog();
        $entity->setDomain('test.com');
        $entity->setClientIp('192.168.1.100');
        $entity->setQueryType(RecordType::AAAA);
        $entity->setResponse('::1');

        $this->assertSame('test.com', $entity->getDomain());
        $this->assertSame('192.168.1.100', $entity->getClientIp());
        $this->assertSame(RecordType::AAAA, $entity->getQueryType());
        $this->assertSame('::1', $entity->getResponse());
    }

    public function testEntityCanHandleDifferentRecordTypes(): void
    {
        $entityA = new DnsQueryLog();
        $entityA->setDomain('a-record.com');
        $entityA->setClientIp('192.168.1.1');
        $entityA->setQueryType(RecordType::A);
        $entityA->setResponse('127.0.0.1');

        $entityAAAA = new DnsQueryLog();
        $entityAAAA->setDomain('aaaa-record.com');
        $entityAAAA->setClientIp('192.168.1.2');
        $entityAAAA->setQueryType(RecordType::AAAA);
        $entityAAAA->setResponse('::1');

        $this->assertSame(RecordType::A, $entityA->getQueryType());
        $this->assertSame(RecordType::AAAA, $entityAAAA->getQueryType());
    }

    public function testEntityCanHandleDifferentDomains(): void
    {
        $entity1 = new DnsQueryLog();
        $entity1->setDomain('target.com');
        $entity1->setClientIp('192.168.1.1');
        $entity1->setQueryType(RecordType::A);
        $entity1->setResponse('127.0.0.1');

        $entity2 = new DnsQueryLog();
        $entity2->setDomain('target.com');
        $entity2->setClientIp('192.168.1.2');
        $entity2->setQueryType(RecordType::AAAA);
        $entity2->setResponse('::1');

        $this->assertSame('target.com', $entity1->getDomain());
        $this->assertSame('target.com', $entity2->getDomain());
    }

    public function testEntityCanHandleDifferentClientIps(): void
    {
        $entity1 = new DnsQueryLog();
        $entity1->setDomain('test1.com');
        $entity1->setClientIp('192.168.1.100');
        $entity1->setQueryType(RecordType::A);
        $entity1->setResponse('127.0.0.1');

        $entity2 = new DnsQueryLog();
        $entity2->setDomain('test2.com');
        $entity2->setClientIp('192.168.1.100');
        $entity2->setQueryType(RecordType::AAAA);
        $entity2->setResponse('::1');

        $this->assertSame('192.168.1.100', $entity1->getClientIp());
        $this->assertSame('192.168.1.100', $entity2->getClientIp());
    }

    public function testFindByClientIp(): void
    {
        $repository = $this->getRepository();

        // 测试查找方法
        $results = $repository->findByClientIp('192.168.1.1');
        $this->assertIsArray($results);

        // 测试带限制的查找
        $results = $repository->findByClientIp('192.168.1.1', 5);
        $this->assertIsArray($results);
        $this->assertLessThanOrEqual(5, count($results));
    }

    public function testFindByDomain(): void
    {
        $repository = $this->getRepository();

        // 测试查找方法
        $results = $repository->findByDomain('example.com');
        $this->assertIsArray($results);

        // 测试带限制的查找
        $results = $repository->findByDomain('example.com', 5);
        $this->assertIsArray($results);
        $this->assertLessThanOrEqual(5, count($results));
    }

    protected function createNewEntity(): object
    {
        $entity = new DnsQueryLog();
        $entity->setDomain('example.com');
        $entity->setClientIp('192.168.1.1');
        $entity->setQueryType(RecordType::A);
        $entity->setResponse('127.0.0.1');

        return $entity;
    }

    protected function getRepository(): DnsQueryLogRepository
    {
        return self::getService(DnsQueryLogRepository::class);
    }
}
