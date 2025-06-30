<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Unit\Exception;

use DnsServerBundle\Exception\ReverseLookupFailure;
use DnsServerBundle\Exception\DnsServerException;
use PHPUnit\Framework\TestCase;

class ReverseLookupFailureTest extends TestCase
{
    public function testExceptionCanBeInstantiated(): void
    {
        $exception = new ReverseLookupFailure('Reverse lookup failed');
        
        $this->assertInstanceOf(ReverseLookupFailure::class, $exception);
        $this->assertInstanceOf(DnsServerException::class, $exception);
        $this->assertSame('Reverse lookup failed', $exception->getMessage());
    }

    public function testExceptionWithCodeAndPrevious(): void
    {
        $previous = new \Exception('PTR record not found');
        $exception = new ReverseLookupFailure('Reverse lookup failed', 404, $previous);
        
        $this->assertSame('Reverse lookup failed', $exception->getMessage());
        $this->assertSame(404, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }
}