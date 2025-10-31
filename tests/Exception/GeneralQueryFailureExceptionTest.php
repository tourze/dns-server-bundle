<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Exception;

use DnsServerBundle\Exception\GeneralQueryFailureException;
use DnsServerBundle\Exception\QueryFailureException;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * @internal
 */
#[CoversClass(GeneralQueryFailureException::class)]
final class GeneralQueryFailureExceptionTest extends AbstractExceptionTestCase
{
    public function testInheritance(): void
    {
        $exception = new GeneralQueryFailureException('Test message');

        self::assertSame('Test message', $exception->getMessage());
    }

    public function testJsonSerialize(): void
    {
        $exception = new GeneralQueryFailureException('Test message', 100);
        $json = $exception->jsonSerialize();

        self::assertSame('Test message', $json['message']);
        self::assertSame(100, $json['code']);
        self::assertIsString($json['file']);
        self::assertIsInt($json['line']);
        self::assertIsString($json['trace']);
    }
}
