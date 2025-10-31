<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Enum;

use DnsServerBundle\Enum\LogLevel;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(LogLevel::class)]
final class LogLevelTest extends AbstractEnumTestCase
{
    public function testEnumValues(): void
    {
        $this->assertSame('debug', LogLevel::DEBUG->value);
        $this->assertSame('info', LogLevel::INFO->value);
        $this->assertSame('notice', LogLevel::NOTICE->value);
        $this->assertSame('warning', LogLevel::WARNING->value);
        $this->assertSame('error', LogLevel::ERROR->value);
        $this->assertSame('critical', LogLevel::CRITICAL->value);
        $this->assertSame('alert', LogLevel::ALERT->value);
        $this->assertSame('emergency', LogLevel::EMERGENCY->value);
    }

    public function testGetDescription(): void
    {
        $this->assertSame('调试信息', LogLevel::DEBUG->getDescription());
        $this->assertSame('一般信息', LogLevel::INFO->getDescription());
        $this->assertSame('通知信息', LogLevel::NOTICE->getDescription());
        $this->assertSame('警告信息', LogLevel::WARNING->getDescription());
        $this->assertSame('错误信息', LogLevel::ERROR->getDescription());
        $this->assertSame('严重错误', LogLevel::CRITICAL->getDescription());
        $this->assertSame('必须立即采取行动', LogLevel::ALERT->getDescription());
        $this->assertSame('系统不可用', LogLevel::EMERGENCY->getDescription());
    }

    public function testGetLabel(): void
    {
        $this->assertSame('调试信息', LogLevel::DEBUG->getLabel());
        $this->assertSame('一般信息', LogLevel::INFO->getLabel());
        $this->assertSame('通知信息', LogLevel::NOTICE->getLabel());
        $this->assertSame('警告信息', LogLevel::WARNING->getLabel());
        $this->assertSame('错误信息', LogLevel::ERROR->getLabel());
        $this->assertSame('严重错误', LogLevel::CRITICAL->getLabel());
        $this->assertSame('必须立即采取行动', LogLevel::ALERT->getLabel());
        $this->assertSame('系统不可用', LogLevel::EMERGENCY->getLabel());
    }

    public function testGetPriority(): void
    {
        $this->assertSame(0, LogLevel::DEBUG->getPriority());
        $this->assertSame(1, LogLevel::INFO->getPriority());
        $this->assertSame(2, LogLevel::NOTICE->getPriority());
        $this->assertSame(3, LogLevel::WARNING->getPriority());
        $this->assertSame(4, LogLevel::ERROR->getPriority());
        $this->assertSame(5, LogLevel::CRITICAL->getPriority());
        $this->assertSame(6, LogLevel::ALERT->getPriority());
        $this->assertSame(7, LogLevel::EMERGENCY->getPriority());
    }

    public function testIsHigherOrEqualThan(): void
    {
        // DEBUG 级别测试
        $this->assertTrue(LogLevel::DEBUG->isHigherOrEqualThan(LogLevel::DEBUG));
        $this->assertFalse(LogLevel::DEBUG->isHigherOrEqualThan(LogLevel::INFO));
        $this->assertFalse(LogLevel::DEBUG->isHigherOrEqualThan(LogLevel::ERROR));

        // INFO 级别测试
        $this->assertTrue(LogLevel::INFO->isHigherOrEqualThan(LogLevel::DEBUG));
        $this->assertTrue(LogLevel::INFO->isHigherOrEqualThan(LogLevel::INFO));
        $this->assertFalse(LogLevel::INFO->isHigherOrEqualThan(LogLevel::WARNING));

        // ERROR 级别测试
        $this->assertTrue(LogLevel::ERROR->isHigherOrEqualThan(LogLevel::DEBUG));
        $this->assertTrue(LogLevel::ERROR->isHigherOrEqualThan(LogLevel::INFO));
        $this->assertTrue(LogLevel::ERROR->isHigherOrEqualThan(LogLevel::WARNING));
        $this->assertTrue(LogLevel::ERROR->isHigherOrEqualThan(LogLevel::ERROR));
        $this->assertFalse(LogLevel::ERROR->isHigherOrEqualThan(LogLevel::CRITICAL));

        // EMERGENCY 级别测试
        $this->assertTrue(LogLevel::EMERGENCY->isHigherOrEqualThan(LogLevel::DEBUG));
        $this->assertTrue(LogLevel::EMERGENCY->isHigherOrEqualThan(LogLevel::ERROR));
        $this->assertTrue(LogLevel::EMERGENCY->isHigherOrEqualThan(LogLevel::ALERT));
        $this->assertTrue(LogLevel::EMERGENCY->isHigherOrEqualThan(LogLevel::EMERGENCY));
    }

    public function testToArray(): void
    {
        // toArray 是实例方法，返回单个枚举项的数组表示
        $array = LogLevel::DEBUG->toArray();

        $this->assertIsArray($array);
        $this->assertCount(2, $array);

        // 验证数组的键值对
        $this->assertArrayHasKey('value', $array);
        $this->assertArrayHasKey('label', $array);

        // 验证值是否正确
        $this->assertSame('debug', $array['value']);
        $this->assertSame('调试信息', $array['label']);

        // 测试其他枚举值
        $errorArray = LogLevel::ERROR->toArray();
        $this->assertSame('error', $errorArray['value']);
        $this->assertSame('错误信息', $errorArray['label']);

        $emergencyArray = LogLevel::EMERGENCY->toArray();
        $this->assertSame('emergency', $emergencyArray['value']);
        $this->assertSame('系统不可用', $emergencyArray['label']);
    }
}
