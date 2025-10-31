<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\DependencyInjection;

use DnsServerBundle\DependencyInjection\DnsServerExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;

/**
 * @internal
 */
#[CoversClass(DnsServerExtension::class)]
final class DnsServerExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
    protected function getExtension(): DnsServerExtension
    {
        return new DnsServerExtension();
    }
}
