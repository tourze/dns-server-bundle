<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Exception;

use DnsServerBundle\Exception\DnsServerException;
use PHPUnit\Framework\TestCase;

class DnsServerExceptionTest extends TestCase
{
    public function testConstructor(): void
    {
        $message = 'Test error message';
        $code = 123;
        $exception = new DnsServerException($message, $code);
        
        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
    }
    
    public function testJsonSerialize(): void
    {
        $message = 'Test error message';
        $code = 123;
        $exception = new DnsServerException($message, $code);
        
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
        $exception = new DnsServerException($message, $code);
        
        $json = json_encode($exception);
        $this->assertNotEmpty($json);
        
        $decoded = json_decode($json, true);
        
        $this->assertArrayHasKey('message', $decoded);
        $this->assertArrayHasKey('code', $decoded);
        $this->assertSame($message, $decoded['message']);
        $this->assertSame($code, $decoded['code']);
    }
}
