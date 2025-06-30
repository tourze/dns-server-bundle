<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Integration\Controller;

use DnsServerBundle\Controller\DnsController;
use DnsServerBundle\Repository\UpstreamDnsServerRepository;
use DnsServerBundle\Service\DnsResolver;
use PHPUnit\Framework\TestCase;

class DnsControllerTest extends TestCase
{
    public function testControllerCanBeInstantiated(): void
    {
        $repository = $this->createMock(UpstreamDnsServerRepository::class);
        $resolver = $this->createMock(DnsResolver::class);
        
        $controller = new DnsController($repository, $resolver);
        
        $this->assertInstanceOf(DnsController::class, $controller);
    }
}