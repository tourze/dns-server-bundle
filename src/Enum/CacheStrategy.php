<?php

namespace DnsServerBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * DNS 缓存策略枚举
 *
 * 定义了 DNS 服务器可用的不同缓存策略，包括无缓存、内存缓存、Redis缓存、文件系统缓存和混合缓存。
 *
 * @see https://datatracker.ietf.org/doc/html/rfc1034#section-4.3.4 DNS缓存
 * @see https://datatracker.ietf.org/doc/html/rfc2308 DNS缓存的否定响应
 */
enum CacheStrategy: string implements Itemable, Labelable, Selectable
{
    use ItemTrait;
    use SelectTrait;
    /**
     * 不缓存
     * 不使用任何缓存，所有查询都直接转发到上游服务器
     * 适用于特殊场景或调试环境
     */
    case NONE = 'none';           // 不缓存

    /**
     * 内存缓存
     * 使用服务器内存存储DNS记录
     * 优点是响应速度快，缺点是服务器重启后缓存丢失
     */
    case MEMORY = 'memory';       // 内存缓存

    /**
     * Redis缓存
     * 使用Redis数据库存储DNS记录
     * 提供持久化存储，适用于高并发场景
     */
    case REDIS = 'redis';         // Redis缓存

    /**
     * 文件系统缓存
     * 使用本地文件系统存储DNS记录
     * 提供基本的持久化存储，适用于单机部署
     */
    case FILESYSTEM = 'file';     // 文件系统缓存

    /**
     * 混合缓存
     * 同时使用内存缓存和持久化存储（Redis或文件系统）
     * 结合了快速访问和持久化的优点
     */
    case HYBRID = 'hybrid';       // 混合缓存 (内存+持久化)

    /**
     * 获取缓存策略的描述
     */
    public function getDescription(): string
    {
        return match($this) {
            self::NONE => '不缓存',
            self::MEMORY => '内存缓存',
            self::REDIS => 'Redis缓存',
            self::FILESYSTEM => '文件系统缓存',
            self::HYBRID => '混合缓存 (内存+持久化)',
        };
    }

    public function getLabel(): string
    {
        return $this->getDescription();
    }

    /**
     * 判断是否启用缓存
     */
    public function isEnabled(): bool
    {
        return $this !== self::NONE;
    }

    /**
     * 判断是否使用持久化存储
     */
    public function isPersistent(): bool
    {
        return in_array($this, [self::REDIS, self::FILESYSTEM, self::HYBRID]);
    }
}
