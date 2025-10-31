<?php

namespace DnsServerBundle\DependencyInjection;

use Tourze\SymfonyDependencyServiceLoader\AutoExtension;

class DnsServerExtension extends AutoExtension
{
    protected function getConfigDir(): string
    {
        return __DIR__ . '/../Resources/config';
    }
}
