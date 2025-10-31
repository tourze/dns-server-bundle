<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests;

use DnsServerBundle\DnsServerBundle;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;

/**
 * @internal
 */
#[CoversClass(DnsServerBundle::class)]
#[RunTestsInSeparateProcesses]
final class DnsServerBundleTest extends AbstractBundleTestCase
{
}
