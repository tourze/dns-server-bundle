<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Integration\Service;

use DnsServerBundle\Service\AttributeControllerLoader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\RouteCollection;

class AttributeControllerLoaderTest extends TestCase
{
    private AttributeControllerLoader $loader;

    protected function setUp(): void
    {
        $this->loader = new AttributeControllerLoader();
    }

    public function testLoaderCanBeInstantiated(): void
    {
        $this->assertInstanceOf(AttributeControllerLoader::class, $this->loader);
    }

    public function testLoad(): void
    {
        $resource = 'test_resource';
        $type = null;
        
        $routes = $this->loader->load($resource, $type);
        
        $this->assertInstanceOf(RouteCollection::class, $routes);
    }

    public function testSupports(): void
    {
        // 根据实际实现，supports 总是返回 false
        $this->assertFalse($this->loader->supports('test_resource', 'dns_attribute'));
        $this->assertFalse($this->loader->supports('test_resource', 'other_type'));
        $this->assertFalse($this->loader->supports('test_resource', null));
    }
}