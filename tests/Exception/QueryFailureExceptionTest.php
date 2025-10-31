<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Exception;

use DnsServerBundle\Exception\DnsServerException;
use DnsServerBundle\Exception\QueryFailureException;
use DnsServerBundle\Exception\ReverseLookupFailureException;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * @internal
 */
#[CoversClass(QueryFailureException::class)]
final class QueryFailureExceptionTest extends AbstractExceptionTestCase
{
    public function testInheritance(): void
    {
        // Test with concrete subclass
        $exception = new ReverseLookupFailureException('Test message');

        self::assertSame('Test message', $exception->getMessage());
    }

    public function testJsonSerialize(): void
    {
        $exception = new ReverseLookupFailureException('Test message', 100);
        $json = $exception->jsonSerialize();

        self::assertSame('Test message', $json['message']);
        self::assertSame(100, $json['code']);
        self::assertIsString($json['file']);
        self::assertIsInt($json['line']);
        self::assertIsString($json['trace']);
    }
}
