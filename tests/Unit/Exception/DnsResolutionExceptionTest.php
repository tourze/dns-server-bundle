<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Unit\Exception;

use DnsServerBundle\Exception\DnsResolutionException;
use DnsServerBundle\Exception\DnsServerException;
use PHPUnit\Framework\TestCase;

class DnsResolutionExceptionTest extends TestCase
{
    public function testExceptionCanBeInstantiated(): void
    {
        $exception = new DnsResolutionException('Resolution failed');
        
        $this->assertInstanceOf(DnsResolutionException::class, $exception);
        $this->assertInstanceOf(DnsServerException::class, $exception);
        $this->assertSame('Resolution failed', $exception->getMessage());
    }

    public function testExceptionWithCodeAndPrevious(): void
    {
        $previous = new \Exception('Timeout error');
        $exception = new DnsResolutionException('Resolution failed', 504, $previous);
        
        $this->assertSame('Resolution failed', $exception->getMessage());
        $this->assertSame(504, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }
}