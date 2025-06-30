<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Unit\Exception;

use DnsServerBundle\Exception\QueryFailure;
use DnsServerBundle\Exception\DnsServerException;
use PHPUnit\Framework\TestCase;

class QueryFailureTest extends TestCase
{
    public function testExceptionCanBeInstantiated(): void
    {
        $exception = new QueryFailure('Query failed');
        
        $this->assertInstanceOf(QueryFailure::class, $exception);
        $this->assertInstanceOf(DnsServerException::class, $exception);
        $this->assertSame('Query failed', $exception->getMessage());
    }

    public function testExceptionWithCodeAndPrevious(): void
    {
        $previous = new \Exception('Network error');
        $exception = new QueryFailure('Query failed', 500, $previous);
        
        $this->assertSame('Query failed', $exception->getMessage());
        $this->assertSame(500, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }
}