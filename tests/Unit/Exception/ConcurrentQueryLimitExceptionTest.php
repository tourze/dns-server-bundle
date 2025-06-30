<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Unit\Exception;

use DnsServerBundle\Exception\ConcurrentQueryLimitException;
use DnsServerBundle\Exception\DnsServerException;
use PHPUnit\Framework\TestCase;

class ConcurrentQueryLimitExceptionTest extends TestCase
{
    public function testExceptionCanBeInstantiated(): void
    {
        $exception = new ConcurrentQueryLimitException('Test message');
        
        $this->assertInstanceOf(ConcurrentQueryLimitException::class, $exception);
        $this->assertInstanceOf(DnsServerException::class, $exception);
        $this->assertSame('Test message', $exception->getMessage());
    }

    public function testExceptionWithCodeAndPrevious(): void
    {
        $previous = new \Exception('Previous exception');
        $exception = new ConcurrentQueryLimitException('Test message', 500, $previous);
        
        $this->assertSame('Test message', $exception->getMessage());
        $this->assertSame(500, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }
}