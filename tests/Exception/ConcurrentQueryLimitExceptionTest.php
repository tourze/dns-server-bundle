<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Exception;

use DnsServerBundle\Exception\ConcurrentQueryLimitException;
use DnsServerBundle\Exception\DnsServerException;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * @internal
 */
#[CoversClass(ConcurrentQueryLimitException::class)]
final class ConcurrentQueryLimitExceptionTest extends AbstractExceptionTestCase
{
    protected function getExceptionClass(): string
    {
        return ConcurrentQueryLimitException::class;
    }

    protected function getParentExceptionClass(): string
    {
        return DnsServerException::class;
    }
}
