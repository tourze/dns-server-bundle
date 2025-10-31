<?php

namespace DnsServerBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * DNS 操作码枚举
 *
 * 定义了 DNS 消息头部中的操作码字段，用于标识不同类型的 DNS 操作。
 * 操作码在 DNS 消息头部占据 4 位，取值范围为 0-15。
 *
 * @see https://datatracker.ietf.org/doc/html/rfc1035#section-4.1.1 DNS消息格式
 * @see https://datatracker.ietf.org/doc/html/rfc3425 非查询操作码
 */
enum OperationCode: int implements Itemable, Labelable, Selectable
{
    use ItemTrait;
    use SelectTrait;
    /**
     * 标准查询
     * DNS 消息中最常见的操作码，用于请求域名解析
     *
     * @see https://datatracker.ietf.org/doc/html/rfc1035#section-4.3
     */
    case QUERY = 0;

    /**
     * 反向查询
     * 用于从 IP 地址查找对应的域名
     * 注意：该操作码已在 RFC3425 中废弃
     *
     * @see https://datatracker.ietf.org/doc/html/rfc3425
     * @deprecated
     */
    case IQUERY = 1;

    /**
     * 服务器状态请求
     * 用于查询 DNS 服务器的当前状态信息
     *
     * @see https://datatracker.ietf.org/doc/html/rfc1035#section-4.3
     */
    case STATUS = 2;

    /**
     * 通知
     * 用于主 DNS 服务器通知从服务器区域发生变化
     *
     * @see https://datatracker.ietf.org/doc/html/rfc1996
     */
    case NOTIFY = 4;

    /**
     * 动态更新
     * 用于动态添加、删除或修改 DNS 记录
     *
     * @see https://datatracker.ietf.org/doc/html/rfc2136
     */
    case UPDATE = 5;

    /**
     * 获取操作码的描述
     */
    public function getDescription(): string
    {
        return match ($this) {
            self::QUERY => '标准查询',
            self::IQUERY => '反向查询',
            self::STATUS => '服务器状态请求',
            self::NOTIFY => '通知',
            self::UPDATE => '动态更新',
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
