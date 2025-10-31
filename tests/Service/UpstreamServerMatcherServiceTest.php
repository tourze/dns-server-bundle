<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Service;

use DnsServerBundle\Entity\UpstreamDnsServer;
use DnsServerBundle\Enum\MatchStrategy;
use DnsServerBundle\Repository\UpstreamDnsServerRepository;
use DnsServerBundle\Service\DnsMatcherService;
use DnsServerBundle\Service\UpstreamServerMatcherService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @internal
 */
#[CoversClass(UpstreamServerMatcherService::class)]
final class UpstreamServerMatcherServiceTest extends TestCase
{
    private UpstreamServerMatcherService $service;

    /** @var UpstreamDnsServerRepository&MockObject */
    private UpstreamDnsServerRepository $repository;

    /** @var DnsMatcherService&MockObject */
    private DnsMatcherService $dnsMatcherService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->initializeTestObjects();
    }

    private function initializeTestObjects(): void
    {
        // Mock UpstreamDnsServerRepository（具体类）
        // 原因：虽然名为 Repository，但这是一个具体类而非接口，Doctrine 的设计模式中 Repository 通常是具体类
        // 合理性：Doctrine Repository 模式就是基于具体类的，这是框架的标准实践
        // 替代方案：可以创建 Repository 接口，但这会偏离 Doctrine 的标准模式，增加不必要的复杂性
        /** @var UpstreamDnsServerRepository&MockObject $repository */
        $repository = $this->createMock(UpstreamDnsServerRepository::class);
        $this->repository = $repository;

        // Mock DnsMatcherService（具体类）
        // 原因：DnsMatcherService 是一个具体服务类，没有定义对应的接口
        // 合理性：在单元测试中 mock 依赖的服务类是标准做法，即使没有接口也是合理的
        // 替代方案：可以为 DnsMatcherService 定义接口（如 DnsMatcherInterface），但需要评估是否有必要增加这层抽象
        /** @var DnsMatcherService&MockObject $dnsMatcherService */
        $dnsMatcherService = $this->createMock(DnsMatcherService::class);
        $this->dnsMatcherService = $dnsMatcherService;

        $this->service = new UpstreamServerMatcherService($this->repository, $this->dnsMatcherService);
    }

    public function testFindMatchingServerWithMatch(): void
    {
        $this->initializeTestObjects();
        $server1 = new UpstreamDnsServer();
        $server1->setPattern('*.example.com');
        $server1->setStrategy(MatchStrategy::WILDCARD);

        $server2 = new UpstreamDnsServer();
        $server2->setPattern('*.google.com');
        $server2->setStrategy(MatchStrategy::WILDCARD);

        $this->repository->expects($this->once())
            ->method('findBy')
            ->with(['valid' => true], ['id' => 'ASC'])
            ->willReturn([$server1, $server2])
        ;

        $this->dnsMatcherService->expects($this->once())
            ->method('isMatch')
            ->with('test.example.com', '*.example.com', MatchStrategy::WILDCARD)
            ->willReturn(true)
        ;

        $result = $this->service->findMatchingServer('test.example.com');

        $this->assertSame($server1, $result);
    }

    public function testFindMatchingServerWithoutMatch(): void
    {
        $this->initializeTestObjects();
        $server1 = new UpstreamDnsServer();
        $server1->setPattern('*.example.com');
        $server1->setStrategy(MatchStrategy::WILDCARD);

        $this->repository->expects($this->once())
            ->method('findBy')
            ->with(['valid' => true], ['id' => 'ASC'])
            ->willReturn([$server1])
        ;

        $this->dnsMatcherService->expects($this->once())
            ->method('isMatch')
            ->with('test.google.com', '*.example.com', MatchStrategy::WILDCARD)
            ->willReturn(false)
        ;

        $result = $this->service->findMatchingServer('test.google.com');

        $this->assertNull($result);
    }

    public function testGetDefaultServer(): void
    {
        $this->initializeTestObjects();
        $defaultServer = new UpstreamDnsServer();
        $defaultServer->setIsDefault(true);

        $this->repository->expects($this->once())
            ->method('getDefaultServer')
            ->willReturn($defaultServer)
        ;

        $result = $this->service->getDefaultServer();

        $this->assertSame($defaultServer, $result);
    }

    public function testFindMatchingOrDefaultServerWithMatch(): void
    {
        $this->initializeTestObjects();
        $matchingServer = new UpstreamDnsServer();
        $matchingServer->setPattern('*.example.com');
        $matchingServer->setStrategy(MatchStrategy::WILDCARD);

        $this->repository->expects($this->once())
            ->method('findBy')
            ->with(['valid' => true], ['id' => 'ASC'])
            ->willReturn([$matchingServer])
        ;

        $this->dnsMatcherService->expects($this->once())
            ->method('isMatch')
            ->with('test.example.com', '*.example.com', MatchStrategy::WILDCARD)
            ->willReturn(true)
        ;

        $result = $this->service->findMatchingOrDefaultServer('test.example.com');

        $this->assertSame($matchingServer, $result);
    }

    public function testFindMatchingOrDefaultServerWithoutMatchButWithDefault(): void
    {
        $this->initializeTestObjects();
        $defaultServer = new UpstreamDnsServer();
        $defaultServer->setIsDefault(true);

        $this->repository->expects($this->once())
            ->method('findBy')
            ->with(['valid' => true], ['id' => 'ASC'])
            ->willReturn([])
        ;

        $this->repository->expects($this->once())
            ->method('getDefaultServer')
            ->willReturn($defaultServer)
        ;

        $result = $this->service->findMatchingOrDefaultServer('test.example.com');

        $this->assertSame($defaultServer, $result);
    }

    public function testFindMatchingOrDefaultServerWithoutMatchAndWithoutDefault(): void
    {
        $this->initializeTestObjects();
        $this->repository->expects($this->once())
            ->method('findBy')
            ->with(['valid' => true], ['id' => 'ASC'])
            ->willReturn([])
        ;

        $this->repository->expects($this->once())
            ->method('getDefaultServer')
            ->willReturn(null)
        ;

        $result = $this->service->findMatchingOrDefaultServer('test.example.com');

        $this->assertNull($result);
    }
}
