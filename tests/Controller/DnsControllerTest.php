<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Controller;

use DnsServerBundle\Controller\DnsController;
use DnsServerBundle\Service\DnsResolver;
use DnsServerBundle\Service\UpstreamServerMatcherService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Tourze\PHPUnitSymfonyWebTest\AbstractWebTestCase;

/**
 * @internal
 */
#[CoversClass(DnsController::class)]
#[RunTestsInSeparateProcesses]
final class DnsControllerTest extends AbstractWebTestCase
{
    public function testControllerCanBeInstantiated(): void
    {
        $upstreamServerMatcherService = self::getService(UpstreamServerMatcherService::class);
        $dnsResolver = self::getService(DnsResolver::class);

        $controller = new DnsController($upstreamServerMatcherService, $dnsResolver);
        $this->assertInstanceOf(DnsController::class, $controller);
    }

    public function testControllerHasGetMethod(): void
    {
        $upstreamServerMatcherService = self::getService(UpstreamServerMatcherService::class);
        $dnsResolver = self::getService(DnsResolver::class);
        $controller = new DnsController($upstreamServerMatcherService, $dnsResolver);

        $reflection = new \ReflectionClass($controller);
        $this->assertTrue($reflection->hasMethod('__invoke'));
    }

    public function testControllerIsPublic(): void
    {
        $upstreamServerMatcherService = self::getService(UpstreamServerMatcherService::class);
        $dnsResolver = self::getService(DnsResolver::class);
        $controller = new DnsController($upstreamServerMatcherService, $dnsResolver);

        $reflection = new \ReflectionClass($controller);
        $this->assertTrue($reflection->isInstantiable());
    }

    public function testControllerMethodIsPublic(): void
    {
        $upstreamServerMatcherService = self::getService(UpstreamServerMatcherService::class);
        $dnsResolver = self::getService(DnsResolver::class);
        $controller = new DnsController($upstreamServerMatcherService, $dnsResolver);

        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('__invoke');
        $this->assertTrue($method->isPublic());
    }

    #[DataProvider('provideNotAllowedMethods')]
    public function testMethodNotAllowed(string $method): void
    {
        $client = self::createClientWithDatabase();

        $this->expectException(MethodNotAllowedHttpException::class);
        $client->request($method, '/dns-query');
    }
}
