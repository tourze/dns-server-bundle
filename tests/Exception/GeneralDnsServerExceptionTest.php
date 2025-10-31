<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Exception;

use DnsServerBundle\Exception\DnsServerException;
use DnsServerBundle\Exception\GeneralDnsServerException;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * @internal
 */
#[CoversClass(GeneralDnsServerException::class)]
final class GeneralDnsServerExceptionTest extends AbstractExceptionTestCase
{
    public function testInheritance(): void
    {
        $exception = new GeneralDnsServerException('Test message');

        self::assertSame('Test message', $exception->getMessage());
    }

    public function testJsonSerialize(): void
    {
        $exception = new GeneralDnsServerException('Test message', 100);
        $json = $exception->jsonSerialize();

        self::assertSame('Test message', $json['message']);
        self::assertSame(100, $json['code']);
        self::assertIsString($json['file']);
        self::assertIsInt($json['line']);
        self::assertIsString($json['trace']);
    }
}
