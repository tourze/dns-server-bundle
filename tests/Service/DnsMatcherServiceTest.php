<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Service;

use DnsServerBundle\Enum\MatchStrategy;
use DnsServerBundle\Service\DnsMatcherService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @internal
 */
#[CoversClass(DnsMatcherService::class)]
final class DnsMatcherServiceTest extends TestCase
{
    private DnsMatcherService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->initializeTestObjects();
    }

    private function initializeTestObjects(): void
    {
        $this->service = new DnsMatcherService();
    }

    public function testMatchExact(): void
    {
        $this->initializeTestObjects();
        $this->assertTrue($this->service->isMatch('example.com', 'example.com', MatchStrategy::EXACT));
        $this->assertTrue($this->service->isMatch('EXAMPLE.com', 'example.COM', MatchStrategy::EXACT));
        $this->assertFalse($this->service->isMatch('sub.example.com', 'example.com', MatchStrategy::EXACT));
    }

    public function testMatchWildcard(): void
    {
        $this->initializeTestObjects();
        $this->assertTrue($this->service->isMatch('example.com', '*.com', MatchStrategy::WILDCARD));
        $this->assertTrue($this->service->isMatch('sub.example.com', '*.example.com', MatchStrategy::WILDCARD));
        $this->assertTrue($this->service->isMatch('example.com', '*', MatchStrategy::WILDCARD));
        $this->assertFalse($this->service->isMatch('example.org', '*.com', MatchStrategy::WILDCARD));
    }

    public function testMatchWildcardWithInvalidPattern(): void
    {
        $this->initializeTestObjects();
        // 测试无效正则表达式的情况
        $originalErrorReporting = error_reporting(E_ALL & ~E_WARNING);
        try {
            $this->assertFalse($this->service->isMatch('example.com', '[', MatchStrategy::WILDCARD));
        } finally {
            error_reporting($originalErrorReporting);
        }
    }

    public function testMatchRegex(): void
    {
        $this->initializeTestObjects();
        $this->assertTrue($this->service->isMatch('example.com', '/example\.com/', MatchStrategy::REGEX));
        $this->assertTrue($this->service->isMatch('sub.example.com', '/.*\.example\.com/', MatchStrategy::REGEX));
        $this->assertFalse($this->service->isMatch('example.org', '/example\.com/', MatchStrategy::REGEX));
    }

    public function testMatchRegexWithInvalidPattern(): void
    {
        $this->initializeTestObjects();
        // 测试无效正则表达式的情况
        $originalErrorReporting = error_reporting(E_ALL & ~E_WARNING);
        try {
            $this->assertFalse($this->service->isMatch('example.com', '[', MatchStrategy::REGEX));
        } finally {
            error_reporting($originalErrorReporting);
        }
    }

    public function testMatchPrefix(): void
    {
        $this->initializeTestObjects();
        $this->assertTrue($this->service->isMatch('example.com', 'example', MatchStrategy::PREFIX));
        $this->assertTrue($this->service->isMatch('EXAMPLE.com', 'example', MatchStrategy::PREFIX));
        $this->assertFalse($this->service->isMatch('sub.example.com', 'example', MatchStrategy::PREFIX));
    }

    public function testMatchSuffix(): void
    {
        $this->initializeTestObjects();
        $this->assertTrue($this->service->isMatch('example.com', '.com', MatchStrategy::SUFFIX));
        $this->assertTrue($this->service->isMatch('sub.example.com', '.example.com', MatchStrategy::SUFFIX));
        $this->assertTrue($this->service->isMatch('example.com', 'example.com', MatchStrategy::SUFFIX));
        $this->assertFalse($this->service->isMatch('example.org', '.com', MatchStrategy::SUFFIX));
    }

    public function testIsMatch(): void
    {
        $this->initializeTestObjects();
        // 测试不同策略的调用
        $this->assertTrue($this->service->isMatch('example.com', 'example.com', MatchStrategy::EXACT));
        $this->assertTrue($this->service->isMatch('example.com', '*.com', MatchStrategy::WILDCARD));
        $this->assertTrue($this->service->isMatch('example.com', '/example\.com/', MatchStrategy::REGEX));
        $this->assertTrue($this->service->isMatch('example.com', 'example', MatchStrategy::PREFIX));
        $this->assertTrue($this->service->isMatch('example.com', '.com', MatchStrategy::SUFFIX));
    }
}
