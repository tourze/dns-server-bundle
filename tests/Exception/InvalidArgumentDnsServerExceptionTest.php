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
#[CoversClass(InvalidArgumentDnsServerException::class)]
final class InvalidArgumentDnsServerExceptionTest extends AbstractExceptionTestCase
{
    protected function getExceptionClass(): string
    {
        return InvalidArgumentDnsServerException::class;
    }

    protected function getParentExceptionClass(): string
    {
        return DnsServerException::class;
    }

    public function testJsonSerialize(): void
    {
        $message = 'Invalid argument';
        $code = 400;

        $exception = new InvalidArgumentDnsServerException($message, $code);
        $json = $exception->jsonSerialize();
        $this->assertArrayHasKey('message', $json);
        $this->assertArrayHasKey('code', $json);
        $this->assertSame($message, $json['message']);
        $this->assertSame($code, $json['code']);
    }
}
