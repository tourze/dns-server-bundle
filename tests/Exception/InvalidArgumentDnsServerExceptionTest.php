<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Exception;

use DnsServerBundle\Exception\DnsServerException;
use DnsServerBundle\Exception\InvalidArgumentDnsServerException;
use PHPUnit\Framework\TestCase;

class InvalidArgumentDnsServerExceptionTest extends TestCase
{
    public function testExceptionInheritance(): void
    {
        $exception = new InvalidArgumentDnsServerException();
        
        // 验证继承关系
        $this->assertInstanceOf(DnsServerException::class, $exception);
    }
    
    public function testConstructor(): void
    {
        $message = 'Invalid argument';
        $code = 400;
        $previous = new \Exception('Previous exception');
        
        $exception = new InvalidArgumentDnsServerException($message, $code, $previous);
        
        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }
    
    public function testJsonSerialize(): void
    {
        $message = 'Invalid argument';
        $code = 400;
        
        $exception = new InvalidArgumentDnsServerException($message, $code);
        $json = $exception->jsonSerialize();
        
        $this->assertIsArray($json);
        $this->assertArrayHasKey('message', $json);
        $this->assertArrayHasKey('code', $json);
        $this->assertSame($message, $json['message']);
        $this->assertSame($code, $json['code']);
    }
}
