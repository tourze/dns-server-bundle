<?php

namespace DnsServerBundle\Tests\Enum;

use DnsServerBundle\Enum\MatchStrategy;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(MatchStrategy::class)]
final class MatchStrategyTest extends AbstractEnumTestCase
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

    public function testGetLabel(): void
    {
        $this->assertEquals('精确匹配', MatchStrategy::EXACT->getLabel());
        $this->assertEquals('正则匹配', MatchStrategy::REGEX->getLabel());
        $this->assertEquals('后缀匹配', MatchStrategy::SUFFIX->getLabel());
        $this->assertEquals('前缀匹配', MatchStrategy::PREFIX->getLabel());
        $this->assertEquals('通配符匹配', MatchStrategy::WILDCARD->getLabel());
    }

    public function testToArray(): void
    {
        // toArray 是实例方法，返回单个枚举项的数组表示
        $array = MatchStrategy::EXACT->toArray();

        $this->assertIsArray($array);
        $this->assertCount(2, $array);

        // 验证数组的键值对
        $this->assertArrayHasKey('value', $array);
        $this->assertArrayHasKey('label', $array);

        // 验证值是否正确
        $this->assertSame('exact', $array['value']);
        $this->assertSame('精确匹配', $array['label']);

        // 测试其他枚举值
        $regexArray = MatchStrategy::REGEX->toArray();
        $this->assertSame('regex', $regexArray['value']);
        $this->assertSame('正则匹配', $regexArray['label']);
    }
}
