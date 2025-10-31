<?php

namespace DnsServerBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * DNS 服务器日志级别枚举
 *
 * 定义了日志记录的不同级别，遵循 RFC 5424 Syslog 协议的日志级别定义。
 * 级别从低到高依次为：DEBUG、INFO、NOTICE、WARNING、ERROR、CRITICAL、ALERT、EMERGENCY。
 *
 * @see https://datatracker.ietf.org/doc/html/rfc5424#section-6.2.1 Syslog 严重性级别
 */
enum LogLevel: string implements Itemable, Labelable, Selectable
{
    use ItemTrait;
    use SelectTrait;
    /**
     * 调试信息
     * 详细的调试信息，用于开发和故障排查
     * 优先级：0（最低）
     */
    case DEBUG = 'debug';

    /**
     * 一般信息
     * 正常运行过程中的重要信息
     * 优先级：1
     */
    case INFO = 'info';

    /**
     * 通知信息
     * 正常但重要的事件
     * 优先级：2
     */
    case NOTICE = 'notice';

    /**
     * 警告信息
     * 不是错误，但可能需要处理的异常情况
     * 优先级：3
     */
    case WARNING = 'warning';

    /**
     * 错误信息
     * 运行时错误，不需要立即处理
     * 优先级：4
     */
    case ERROR = 'error';

    /**
     * 严重错误
     * 需要尽快处理的危险情况
     * 优先级：5
     */
    case CRITICAL = 'critical';

    /**
     * 必须立即采取行动
     * 系统存在严重问题，需要立即处理
     * 优先级：6
     */
    case ALERT = 'alert';

    /**
     * 系统不可用
     * 系统完全不可用，最高级别的错误
     * 优先级：7（最高）
     */
    case EMERGENCY = 'emergency';

    /**
     * 获取日志级别的描述
     *
     * @return string 返回当前日志级别的中文描述
     */
    public function getDescription(): string
    {
        return match ($this) {
            self::DEBUG => '调试信息',
            self::INFO => '一般信息',
            self::NOTICE => '通知信息',
            self::WARNING => '警告信息',
            self::ERROR => '错误信息',
            self::CRITICAL => '严重错误',
            self::ALERT => '必须立即采取行动',
            self::EMERGENCY => '系统不可用',
        };
    }

    /**
     * 获取标签
     */
    public function getLabel(): string
    {
        return $this->getDescription();
    }

    /**
     * 获取日志级别的优先级
     *
     * @return int 返回日志级别的优先级，数字越大优先级越高（0-7）
     *
     * @see https://datatracker.ietf.org/doc/html/rfc5424#section-6.2.1
     */
    public function getPriority(): int
    {
        return match ($this) {
            self::DEBUG => 0,
            self::INFO => 1,
            self::NOTICE => 2,
            self::WARNING => 3,
            self::ERROR => 4,
            self::CRITICAL => 5,
            self::ALERT => 6,
            self::EMERGENCY => 7,
        };
    }

    /**
     * 判断当前日志级别是否高于或等于指定级别
     *
     * @param self $level 要比较的日志级别
     *
     * @return bool 如果当前级别高于或等于指定级别返回true，否则返回false
     */
    public function isHigherOrEqualThan(self $level): bool
    {
        return $this->getPriority() >= $level->getPriority();
    }
}
