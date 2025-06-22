<?php

namespace DnsServerBundle\Tests\Enum;

use DnsServerBundle\Enum\MatchStrategy;
use PHPUnit\Framework\TestCase;

class MatchStrategyTest extends TestCase
{
    public function testEnumExists(): void
    {
        $this->assertTrue(enum_exists(MatchStrategy::class));
    }

    public function testEnumValues(): void
    {
        $this->assertSame('EXACT', MatchStrategy::EXACT->name);
        $this->assertSame('REGEX', MatchStrategy::REGEX->name);
        $this->assertSame('SUFFIX', MatchStrategy::SUFFIX->name);
        $this->assertSame('PREFIX', MatchStrategy::PREFIX->name);
        $this->assertSame('WILDCARD', MatchStrategy::WILDCARD->name);
    }

    public function testTryFromWithValidValue(): void
    {
        $this->assertEquals(MatchStrategy::EXACT, MatchStrategy::tryFrom('exact'));
        $this->assertEquals(MatchStrategy::REGEX, MatchStrategy::tryFrom('regex'));
        $this->assertEquals(MatchStrategy::SUFFIX, MatchStrategy::tryFrom('suffix'));
        $this->assertEquals(MatchStrategy::PREFIX, MatchStrategy::tryFrom('prefix'));
        $this->assertEquals(MatchStrategy::WILDCARD, MatchStrategy::tryFrom('wildcard'));
    }

    public function testTryFromWithInvalidValue(): void
    {
        $this->assertNull(MatchStrategy::tryFrom('INVALID'));
        $this->assertNull(MatchStrategy::tryFrom(''));
    }
    
    public function testGetLabel(): void
    {
        $this->assertEquals('精确匹配', MatchStrategy::EXACT->getLabel());
        $this->assertEquals('正则匹配', MatchStrategy::REGEX->getLabel());
        $this->assertEquals('后缀匹配', MatchStrategy::SUFFIX->getLabel());
        $this->assertEquals('前缀匹配', MatchStrategy::PREFIX->getLabel());
        $this->assertEquals('通配符匹配', MatchStrategy::WILDCARD->getLabel());
    }
} 