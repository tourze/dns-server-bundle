<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Exception;

use DnsServerBundle\Exception\DnsResolutionException;
use DnsServerBundle\Exception\DnsServerException;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * @internal
 */
#[CoversClass(DnsResolutionException::class)]
final class DnsResolutionExceptionTest extends AbstractExceptionTestCase
{
    protected function getExceptionClass(): string
    {
        return DnsResolutionException::class;
    }

    protected function getParentExceptionClass(): string
    {
        return DnsServerException::class;
    }
}
