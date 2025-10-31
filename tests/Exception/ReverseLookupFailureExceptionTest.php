<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Exception;

use DnsServerBundle\Exception\QueryFailureException;
use DnsServerBundle\Exception\ReverseLookupFailureException;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * @internal
 */
#[CoversClass(ReverseLookupFailureException::class)]
final class ReverseLookupFailureExceptionTest extends AbstractExceptionTestCase
{
    protected function getExceptionClass(): string
    {
        return ReverseLookupFailureException::class;
    }

    protected function getParentExceptionClass(): string
    {
        return QueryFailureException::class;
    }
}
