<?php

namespace DnsServerBundle\Enum;

/**
 * DNS 查询模式枚举
 *
 * 定义了 DNS 查询的两种基本模式：递归查询和迭代查询。
 *
 * @see https://datatracker.ietf.org/doc/html/rfc1034#section-3.7 DNS查询处理
 * @see https://datatracker.ietf.org/doc/html/rfc1035#section-7.1 递归查询
 */
enum QueryMode: string
{
    /**
     * 递归查询
     * DNS服务器会代表客户端完成整个解析过程，直到获得最终结果
     * 适用于客户端请求，如本地DNS服务器为用户提供服务
     * @see https://datatracker.ietf.org/doc/html/rfc1035#section-4.3.2
     */
    case RECURSIVE = 'recursive';

    /**
     * 迭代查询
     * DNS服务器返回最接近的下一级nameserver信息，由客户端继续查询
     * 适用于DNS服务器之间的查询
     * @see https://datatracker.ietf.org/doc/html/rfc1034#section-4.3.1
     */
    case ITERATIVE = 'iterative';

    /**
     * 获取查询模式的描述
     */
    public function getDescription(): string
    {
        return match($this) {
            self::RECURSIVE => '递归查询',
            self::ITERATIVE => '迭代查询',
        };
    }

    /**
     * 判断是否为递归查询
     */
    public function isRecursive(): bool
    {
        return $this === self::RECURSIVE;
    }

    /**
     * 判断是否为迭代查询
     */
    public function isIterative(): bool
    {
        return $this === self::ITERATIVE;
    }
}
