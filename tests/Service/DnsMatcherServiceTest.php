<?php

namespace DnsServerBundle\Tests\Service;

use DnsServerBundle\Enum\MatchStrategy;
use DnsServerBundle\Service\DnsMatcherService;
use PHPUnit\Framework\TestCase;

class DnsMatcherServiceTest extends TestCase
{
    private DnsMatcherService $service;
    
    protected function setUp(): void
    {
        $this->service = new DnsMatcherService();
    }
    
    public function testIsMatch_WithExactMatch(): void
    {
        $this->assertTrue($this->service->isMatch('example.com', 'example.com', MatchStrategy::EXACT));
        $this->assertFalse($this->service->isMatch('sub.example.com', 'example.com', MatchStrategy::EXACT));
        $this->assertFalse($this->service->isMatch('anotherexample.com', 'example.com', MatchStrategy::EXACT));
    }
    
    public function testIsMatch_WithSuffixMatch(): void
    {
        $this->assertTrue($this->service->isMatch('example.com', '.example.com', MatchStrategy::SUFFIX));
        $this->assertTrue($this->service->isMatch('sub.example.com', '.example.com', MatchStrategy::SUFFIX));
        $this->assertTrue($this->service->isMatch('deep.sub.example.com', '.example.com', MatchStrategy::SUFFIX));
        $this->assertFalse($this->service->isMatch('anotherexample.com', '.example.com', MatchStrategy::SUFFIX));
        $this->assertFalse($this->service->isMatch('example.org', '.example.com', MatchStrategy::SUFFIX));
    }
    
    public function testIsMatch_WithPrefixMatch(): void
    {
        $this->assertTrue($this->service->isMatch('example.com', 'example', MatchStrategy::PREFIX));
        $this->assertTrue($this->service->isMatch('example.org', 'example', MatchStrategy::PREFIX));
        $this->assertFalse($this->service->isMatch('sub.example.com', 'example', MatchStrategy::PREFIX));
    }
    
    public function testIsMatch_WithWildcardMatch(): void
    {
        $this->assertTrue($this->service->isMatch('example.com', '*.com', MatchStrategy::WILDCARD));
        $this->assertTrue($this->service->isMatch('sub.example.com', '*example.com', MatchStrategy::WILDCARD));
        $this->assertFalse($this->service->isMatch('example.org', '*.com', MatchStrategy::WILDCARD));
    }
    
    public function testIsMatch_WithRegexMatch(): void
    {
        $this->assertTrue($this->service->isMatch('sub.example.com', '/^[a-z]+\.example\.com$/', MatchStrategy::REGEX));
        $this->assertTrue($this->service->isMatch('test.example.org', '/^test\.example\.(com|org)$/', MatchStrategy::REGEX));
        $this->assertFalse($this->service->isMatch('example.com', '/^[a-z]+\.example\.com$/', MatchStrategy::REGEX));
        $this->assertFalse($this->service->isMatch('deep.sub.example.com', '/^[a-z]+\.example\.com$/', MatchStrategy::REGEX));
        $this->assertFalse($this->service->isMatch('example.net', '/^[a-z]+\.example\.(com|org)$/', MatchStrategy::REGEX));
    }
    
    public function testIsMatch_WithInvalidRegex(): void
    {
        $this->assertFalse($this->service->isMatch('example.com', '/[unclosed regex', MatchStrategy::REGEX));
    }
} 