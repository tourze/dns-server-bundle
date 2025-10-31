<?php

namespace DnsServerBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * DNS 服务器安全策略枚举
 *
 * 定义了 DNS 服务器可用的不同安全级别和验证策略。
 *
 * @see https://datatracker.ietf.org/doc/html/rfc4033 DNSSEC协议
 * @see https://datatracker.ietf.org/doc/html/rfc4035 DNSSEC协议实现
 */
enum SecurityPolicy: string implements Itemable, Labelable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    /**
     * 开放模式
     * 最基本的安全级别，接受所有DNS请求，不进行额外的安全验证
     * 适用于内部网络或测试环境
     */
    case OPEN = 'open';               // 开放模式，接受所有请求

    /**
     * 限制模式
     * 中等安全级别，通过IP白名单或其他方式限制请求来源
     * 适用于需要基本访问控制的环境
     */
    case RESTRICTED = 'restricted';   // 限制模式，只接受特定来源的请求

    /**
     * DNSSEC模式
     * 高安全级别，启用DNSSEC验证确保DNS应答的真实性
     *
     * @see https://datatracker.ietf.org/doc/html/rfc4033#section-3
     */
    case DNSSEC = 'dnssec';           // DNSSEC模式，支持DNSSEC验证

    /**
     * 严格模式
     * 最高安全级别，同时启用来源验证和DNSSEC验证
     * 适用于需要最高安全保障的生产环境
     */
    case STRICT = 'strict';           // 严格模式，完全验证所有请求

    /**
     * 获取安全策略的描述
     */
    public function getDescription(): string
    {
        return match ($this) {
            self::OPEN => '开放模式，接受所有请求',
            self::RESTRICTED => '限制模式，只接受特定来源的请求',
            self::DNSSEC => 'DNSSEC模式，支持DNSSEC验证',
            self::STRICT => '严格模式，完全验证所有请求',
        };
    }

    /**
     * 判断是否需要验证请求来源
     */
    public function shouldValidateSource(): bool
    {
        return in_array($this, [self::RESTRICTED, self::STRICT], true);
    }

    /**
     * 判断是否需要DNSSEC验证
     */
    public function requiresDnssec(): bool
    {
        return in_array($this, [self::DNSSEC, self::STRICT], true);
    }

    /**
     * 获取标签
     */
    public function getLabel(): string
    {
        return $this->getDescription();
    }
}
