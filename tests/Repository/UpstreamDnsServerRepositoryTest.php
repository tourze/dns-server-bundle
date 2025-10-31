<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Repository;

use DnsServerBundle\Entity\UpstreamDnsServer;
use DnsServerBundle\Enum\MatchStrategy;
use DnsServerBundle\Repository\UpstreamDnsServerRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(UpstreamDnsServerRepository::class)]
#[RunTestsInSeparateProcesses]
final class UpstreamDnsServerRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
        // Repository 测试设置
    }

    public function testEntityCanBeCreated(): void
    {
        $entity = new UpstreamDnsServer();
        $entity->setName('Test DNS Server');
        $entity->setHost('192.168.1.100');
        $entity->setPort(53);
        $entity->setTimeout(5);
        $entity->setWeight(100);
        $entity->setPattern('*.example.com');
        $entity->setStrategy(MatchStrategy::WILDCARD);
        $entity->setIsDefault(false);
        $entity->setTtl(300);
        $entity->setValid(true);
        $entity->setVerifyCert(true);

        $this->assertSame('Test DNS Server', $entity->getName());
        $this->assertSame('192.168.1.100', $entity->getHost());
        $this->assertSame(53, $entity->getPort());
        $this->assertSame(5, $entity->getTimeout());
        $this->assertSame(100, $entity->getWeight());
        $this->assertSame('*.example.com', $entity->getPattern());
        $this->assertSame(MatchStrategy::WILDCARD, $entity->getStrategy());
        $this->assertFalse($entity->isDefault());
        $this->assertSame(300, $entity->getTtl());
        $this->assertTrue($entity->isValid());
        $this->assertTrue($entity->isVerifyCert());
    }

    public function testEntityCanHaveDescription(): void
    {
        $entity = new UpstreamDnsServer();
        $entity->setName('Server with description');
        $entity->setHost('11.11.11.11');
        $entity->setPort(53);
        $entity->setValid(true);
        $entity->setWeight(100);
        $entity->setPattern('*');
        $entity->setStrategy(MatchStrategy::WILDCARD);
        $entity->setDescription('Has description');

        $this->assertSame('Has description', $entity->getDescription());
    }

    public function testEntityCanHaveNullDescription(): void
    {
        $entity = new UpstreamDnsServer();
        $entity->setName('Server without description');
        $entity->setHost('12.12.12.12');
        $entity->setPort(53);
        $entity->setValid(true);
        $entity->setWeight(100);
        $entity->setPattern('*');
        $entity->setStrategy(MatchStrategy::WILDCARD);
        $entity->setDescription(null);

        $this->assertNull($entity->getDescription());
    }

    public function testEntityCanSetValidFlag(): void
    {
        $validEntity = new UpstreamDnsServer();
        $validEntity->setName('Valid Server');
        $validEntity->setHost('13.13.13.13');
        $validEntity->setPort(53);
        $validEntity->setValid(true);
        $validEntity->setWeight(100);
        $validEntity->setPattern('*');
        $validEntity->setStrategy(MatchStrategy::WILDCARD);

        $invalidEntity = new UpstreamDnsServer();
        $invalidEntity->setName('Invalid Server');
        $invalidEntity->setHost('14.14.14.14');
        $invalidEntity->setPort(53);
        $invalidEntity->setValid(false);
        $invalidEntity->setWeight(50);
        $invalidEntity->setPattern('*');
        $invalidEntity->setStrategy(MatchStrategy::WILDCARD);

        $this->assertTrue($validEntity->isValid());
        $this->assertFalse($invalidEntity->isValid());
    }

    public function testEntityCanSetDefaultFlag(): void
    {
        $defaultEntity = new UpstreamDnsServer();
        $defaultEntity->setName('Default Server');
        $defaultEntity->setHost('15.15.15.15');
        $defaultEntity->setPort(53);
        $defaultEntity->setValid(true);
        $defaultEntity->setWeight(100);
        $defaultEntity->setPattern('*');
        $defaultEntity->setStrategy(MatchStrategy::WILDCARD);
        $defaultEntity->setIsDefault(true);

        $nonDefaultEntity = new UpstreamDnsServer();
        $nonDefaultEntity->setName('Non-Default Server');
        $nonDefaultEntity->setHost('16.16.16.16');
        $nonDefaultEntity->setPort(53);
        $nonDefaultEntity->setValid(true);
        $nonDefaultEntity->setWeight(90);
        $nonDefaultEntity->setPattern('*');
        $nonDefaultEntity->setStrategy(MatchStrategy::WILDCARD);
        $nonDefaultEntity->setIsDefault(false);

        $this->assertTrue($defaultEntity->isDefault());
        $this->assertFalse($nonDefaultEntity->isDefault());
    }

    public function testFindAllValid(): void
    {
        $repository = $this->getRepository();

        // 测试查找所有有效的上游DNS服务器
        $results = $repository->findAllValid();
        $this->assertIsArray($results);

        // 验证所有返回的服务器都是有效的
        foreach ($results as $server) {
            $this->assertInstanceOf(UpstreamDnsServer::class, $server);
            $this->assertTrue($server->isValid());
        }
    }

    protected function createNewEntity(): object
    {
        $entity = new UpstreamDnsServer();
        $entity->setName('Test DNS Server');
        $entity->setHost('192.168.1.100');
        $entity->setPort(53);
        $entity->setTimeout(5);
        $entity->setWeight(100);
        $entity->setPattern('*.example.com');
        $entity->setStrategy(MatchStrategy::WILDCARD);
        $entity->setIsDefault(false);
        $entity->setTtl(300);
        $entity->setValid(true);
        $entity->setVerifyCert(true);

        return $entity;
    }

    protected function getRepository(): UpstreamDnsServerRepository
    {
        return self::getService(UpstreamDnsServerRepository::class);
    }
}
