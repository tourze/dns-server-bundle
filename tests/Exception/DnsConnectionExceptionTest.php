<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Exception;

use DnsServerBundle\Exception\DnsConnectionException;
use DnsServerBundle\Exception\DnsServerException;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * @internal
 */
#[CoversClass(DnsConnectionException::class)]
final class DnsConnectionExceptionTest extends AbstractExceptionTestCase
{
    protected function getExceptionClass(): string
    {
        return DnsConnectionException::class;
    }

    protected function getParentExceptionClass(): string
    {
        return DnsServerException::class;
    }
}
