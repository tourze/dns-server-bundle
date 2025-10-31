<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Exception;

use DnsServerBundle\Exception\DnsServerException;
use DnsServerBundle\Exception\InvalidArgumentDnsServerException;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * @internal
 */
#[CoversClass(DnsServerException::class)]
final class DnsServerExceptionTest extends AbstractExceptionTestCase
{
    protected function getExceptionClass(): string
    {
        return DnsServerException::class;
    }

    protected function getParentExceptionClass(): string
    {
        return \Exception::class;
    }

    public function testJsonSerialize(): void
    {
        $message = 'Test error message';
        $code = 123;
        $exception = new InvalidArgumentDnsServerException($message, $code);

        $json = $exception->jsonSerialize();
        $this->assertArrayHasKey('message', $json);
        $this->assertArrayHasKey('code', $json);
        $this->assertArrayHasKey('file', $json);
        $this->assertArrayHasKey('line', $json);
        $this->assertArrayHasKey('trace', $json);

        $this->assertSame($message, $json['message']);
        $this->assertSame($code, $json['code']);
        $this->assertIsInt($json['line']);
    }

    public function testJsonEncode(): void
    {
        $message = 'Test error message';
        $code = 123;
        $exception = new InvalidArgumentDnsServerException($message, $code);

        $json = json_encode($exception);
        $this->assertNotEmpty($json);

        $decoded = json_decode($json, true);
        $this->assertIsArray($decoded);

        $this->assertArrayHasKey('message', $decoded);
        $this->assertArrayHasKey('code', $decoded);
        $this->assertSame($message, $decoded['message']);
        $this->assertSame($code, $decoded['code']);
    }
}
