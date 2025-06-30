<?php

namespace DnsServerBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * DNS 查询类型枚举
 *
 * 定义了 DNS 查询操作中可能的查询类型。每种类型代表不同的 DNS 操作目的。
 *
 * @see https://datatracker.ietf.org/doc/html/rfc1035 DNS协议规范
 * @see https://datatracker.ietf.org/doc/html/rfc2136 DNS动态更新
 * @see https://datatracker.ietf.org/doc/html/rfc1996 DNS NOTIFY机制
 */
enum QueryType: int implements Itemable, Labelable, Selectable
{
    use ItemTrait;
    use SelectTrait;
    /**
     * 标准查询
     * 最常见的DNS查询类型，用于解析域名到IP地址或其他记录
     * @see https://datatracker.ietf.org/doc/html/rfc1035#section-4.1.1
     */
    case STANDARD = 0;    // 标准查询

    /**
     * 反向查询
     * 用于从IP地址查找对应的域名
     * @see https://datatracker.ietf.org/doc/html/rfc1035#section-3.5
     */
    case INVERSE = 1;     // 反向查询

    /**
     * 服务器状态请求
     * 用于查询DNS服务器的当前状态和统计信息
     */
    case SERVER_STATUS = 2; // 服务器状态请求

    /**
     * 区域变更通知
     * 用于主DNS服务器通知从服务器区域发生变化
     * @see https://datatracker.ietf.org/doc/html/rfc1996
     */
    case NOTIFY = 4;      // 区域变更通知

    /**
     * 动态更新请求
     * 用于动态添加或删除DNS记录
     * @see https://datatracker.ietf.org/doc/html/rfc2136
     */
    case UPDATE = 5;      // 动态更新请求

    /**
     * 获取查询类型的描述
     */
    public function getDescription(): string
    {
        return match($this) {
            self::STANDARD => '标准查询',
            self::INVERSE => '反向查询',
            self::SERVER_STATUS => '服务器状态请求',
            self::NOTIFY => '区域变更通知',
            self::UPDATE => '动态更新请求',
        };
    }

    /**
     * 获取标签
     */
    public function getLabel(): string
    {
        return $this->getDescription();
    }
}
