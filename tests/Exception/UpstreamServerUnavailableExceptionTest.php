<?php

namespace DnsServerBundle\Tests\Exception;

use DnsServerBundle\Exception\DnsServerException;
use DnsServerBundle\Exception\UpstreamServerUnavailableException;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * @internal
 */
#[CoversClass(UpstreamServerUnavailableException::class)]
final class UpstreamServerUnavailableExceptionTest extends AbstractExceptionTestCase
{
    public function testConstruct(): void
    {
        $message = 'Test message';
        $code = 123;
        $exception = new UpstreamServerUnavailableException($message, $code);

        self::assertSame($message, $exception->getMessage());
        self::assertSame($code, $exception->getCode());
    }

    public function testJsonSerialize(): void
    {
        $message = 'Test message';
        $code = 123;
        $exception = new UpstreamServerUnavailableException($message, $code);

        $json = $exception->jsonSerialize();

        self::assertIsArray($json);
        self::assertArrayHasKey('message', $json);
        self::assertArrayHasKey('code', $json);
        self::assertArrayHasKey('file', $json);
        self::assertArrayHasKey('line', $json);
        self::assertArrayHasKey('trace', $json);
        self::assertSame($message, $json['message']);
        self::assertSame($code, $json['code']);
    }

    protected function createNewEntity(): object
    {
        return new UpstreamServerUnavailableException('Test exception message', 500);
    }
}
