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
    
    public function testMatchExact(): void
    {
        $this->assertTrue($this->service->isMatch('example.com', 'example.com', MatchStrategy::EXACT));
        $this->assertTrue($this->service->isMatch('EXAMPLE.com', 'example.COM', MatchStrategy::EXACT));
        $this->assertFalse($this->service->isMatch('sub.example.com', 'example.com', MatchStrategy::EXACT));
    }
    
    public function testMatchWildcard(): void
    {
        $this->assertTrue($this->service->isMatch('example.com', '*.com', MatchStrategy::WILDCARD));
        $this->assertTrue($this->service->isMatch('sub.example.com', '*.example.com', MatchStrategy::WILDCARD));
        $this->assertTrue($this->service->isMatch('example.com', '*', MatchStrategy::WILDCARD));
        $this->assertFalse($this->service->isMatch('example.org', '*.com', MatchStrategy::WILDCARD));
    }
    
    public function testMatchWildcardWithInvalidPattern(): void
    {
        // 测试无效正则表达式的情况
        $this->assertFalse($this->service->isMatch('example.com', '[', MatchStrategy::WILDCARD));
    }
    
    public function testMatchRegex(): void
    {
        $this->assertTrue($this->service->isMatch('example.com', '/example\.com/', MatchStrategy::REGEX));
        $this->assertTrue($this->service->isMatch('sub.example.com', '/.*\.example\.com/', MatchStrategy::REGEX));
        $this->assertFalse($this->service->isMatch('example.org', '/example\.com/', MatchStrategy::REGEX));
    }
    
    public function testMatchRegexWithInvalidPattern(): void
    {
        // 测试无效正则表达式的情况
        $this->assertFalse($this->service->isMatch('example.com', '[', MatchStrategy::REGEX));
    }
    
    public function testMatchPrefix(): void
    {
        $this->assertTrue($this->service->isMatch('example.com', 'example', MatchStrategy::PREFIX));
        $this->assertTrue($this->service->isMatch('EXAMPLE.com', 'example', MatchStrategy::PREFIX));
        $this->assertFalse($this->service->isMatch('sub.example.com', 'example', MatchStrategy::PREFIX));
    }
    
    public function testMatchSuffix(): void
    {
        $this->assertTrue($this->service->isMatch('example.com', '.com', MatchStrategy::SUFFIX));
        $this->assertTrue($this->service->isMatch('sub.example.com', '.example.com', MatchStrategy::SUFFIX));
        $this->assertTrue($this->service->isMatch('example.com', 'example.com', MatchStrategy::SUFFIX));
        $this->assertFalse($this->service->isMatch('example.org', '.com', MatchStrategy::SUFFIX));
    }
    
    public function testIsMatch(): void
    {
        // 测试不同策略的调用
        $this->assertTrue($this->service->isMatch('example.com', 'example.com', MatchStrategy::EXACT));
        $this->assertTrue($this->service->isMatch('example.com', '*.com', MatchStrategy::WILDCARD));
        $this->assertTrue($this->service->isMatch('example.com', '/example\.com/', MatchStrategy::REGEX));
        $this->assertTrue($this->service->isMatch('example.com', 'example', MatchStrategy::PREFIX));
        $this->assertTrue($this->service->isMatch('example.com', '.com', MatchStrategy::SUFFIX));
    }
} 