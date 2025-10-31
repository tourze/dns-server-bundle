<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Service;

use DnsServerBundle\Service\AttributeControllerLoader;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Routing\RouteCollection;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(AttributeControllerLoader::class)]
#[RunTestsInSeparateProcesses]
final class AttributeControllerLoaderTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // Service integration test setup
    }

    public function testLoaderCanBeInstantiated(): void
    {
        $loader = self::getService(AttributeControllerLoader::class);
        $this->assertInstanceOf(AttributeControllerLoader::class, $loader);
    }

    public function testSupports(): void
    {
        $loader = self::getService(AttributeControllerLoader::class);
        $this->assertFalse($loader->supports('test_resource', 'dns_attribute'));
        $this->assertFalse($loader->supports('test_resource', 'other_type'));
        $this->assertFalse($loader->supports('test_resource', null));
    }

    public function testAutoload(): void
    {
        $loader = self::getService(AttributeControllerLoader::class);
        $routes = $loader->autoload();

        $this->assertInstanceOf(RouteCollection::class, $routes);
        $this->assertGreaterThan(0, $routes->count());
    }
}
