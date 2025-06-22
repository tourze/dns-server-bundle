<?php

namespace DnsServerBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * DNS 传输协议枚举
 *
 * 定义了 DNS 服务支持的传输层协议。
 * DNS 默认使用 UDP 协议进行查询，但在某些情况下会切换到 TCP。
 *
 * @see https://datatracker.ietf.org/doc/html/rfc1035#section-4.2 传输协议
 * @see https://datatracker.ietf.org/doc/html/rfc7766 DNS over TCP 要求
 */
enum Protocol: string implements Itemable, Labelable, Selectable
{
    use ItemTrait;
    use SelectTrait;
    /**
     * UDP协议
     * DNS 查询的主要传输协议，适用于小于 512 字节的消息
     * @see https://datatracker.ietf.org/doc/html/rfc1035#section-4.2.1
     */
    case UDP = 'udp';

    /**
     * TCP协议
     * 用于大于 512 字节的消息传输，或需要可靠传输的场景
     * 如：区域传送(AXFR)、DNSSEC 响应等
     * @see https://datatracker.ietf.org/doc/html/rfc7766#section-1
     */
    case TCP = 'tcp';

    /**
     * 获取协议的描述
     */
    public function getDescription(): string
    {
        return match($this) {
            self::UDP => 'UDP协议',
            self::TCP => 'TCP协议',
        };
    }

    /**
     * 获取协议的默认端口
     * @see https://datatracker.ietf.org/doc/html/rfc1035#section-4.2.1
     */
    public function getDefaultPort(): int
    {
        return 53; // DNS默认端口都是53
    }

    /**
     * 判断是否为UDP协议
     */
    public function isUdp(): bool
    {
        return $this === self::UDP;
    }

    /**
     * 判断是否为TCP协议
     */
    public function isTcp(): bool
    {
        return $this === self::TCP;
    }

    /**
     * 获取标签
     */
    public function getLabel(): string
    {
        return $this->getDescription();
    }
}
