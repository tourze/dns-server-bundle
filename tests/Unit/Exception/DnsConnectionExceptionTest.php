<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Unit\Exception;

use DnsServerBundle\Exception\DnsConnectionException;
use DnsServerBundle\Exception\DnsServerException;
use PHPUnit\Framework\TestCase;

class DnsConnectionExceptionTest extends TestCase
{
    public function testExceptionCanBeInstantiated(): void
    {
        $exception = new DnsConnectionException('Connection failed');
        
        $this->assertInstanceOf(DnsConnectionException::class, $exception);
        $this->assertInstanceOf(DnsServerException::class, $exception);
        $this->assertSame('Connection failed', $exception->getMessage());
    }

    public function testExceptionWithCodeAndPrevious(): void
    {
        $previous = new \Exception('Socket error');
        $exception = new DnsConnectionException('Connection failed', 500, $previous);
        
        $this->assertSame('Connection failed', $exception->getMessage());
        $this->assertSame(500, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }
}