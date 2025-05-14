<?php

namespace DnsServerBundle\Enum;

/**
 * DNS 响应码枚举
 *
 * 定义了 DNS 响应消息中的响应码字段，用于表示查询处理的结果状态。
 * 响应码在 DNS 消息头部占据 4 位，取值范围为 0-15（基础），16-23（扩展）。
 *
 * @see https://datatracker.ietf.org/doc/html/rfc1035#section-4.1.1 基础响应码
 * @see https://datatracker.ietf.org/doc/html/rfc6895#section-2.3 扩展响应码
 */
enum ResponseCode: int
{
    /**
     * 没有错误
     * 查询成功完成
     * @see https://datatracker.ietf.org/doc/html/rfc1035#section-4.1.1
     */
    case NOERROR = 0;

    /**
     * 格式错误
     * 名称服务器无法解释查询
     * @see https://datatracker.ietf.org/doc/html/rfc1035#section-4.1.1
     */
    case FORMERR = 1;

    /**
     * 服务器失败
     * 服务器处理查询时遇到问题
     * @see https://datatracker.ietf.org/doc/html/rfc1035#section-4.1.1
     */
    case SERVFAIL = 2;

    /**
     * 不存在的域名
     * 查询的域名不存在
     * @see https://datatracker.ietf.org/doc/html/rfc1035#section-4.1.1
     */
    case NXDOMAIN = 3;

    /**
     * 未实现
     * 名称服务器不支持请求的查询类型
     * @see https://datatracker.ietf.org/doc/html/rfc1035#section-4.1.1
     */
    case NOTIMP = 4;

    /**
     * 拒绝查询
     * 名称服务器因策略原因拒绝执行请求的操作
     * @see https://datatracker.ietf.org/doc/html/rfc1035#section-4.1.1
     */
    case REFUSED = 5;

    /**
     * 域名存在但不应该
     * 动态更新时，某个不应该存在的名称却存在
     * @see https://datatracker.ietf.org/doc/html/rfc2136#section-2
     */
    case YXDOMAIN = 6;

    /**
     * RR集合存在但不应该
     * 动态更新时，某个不应该存在的RR集合却存在
     * @see https://datatracker.ietf.org/doc/html/rfc2136#section-2
     */
    case YXRRSET = 7;

    /**
     * RR集合不存在
     * 动态更新时，某个应该存在的RR集合不存在
     * @see https://datatracker.ietf.org/doc/html/rfc2136#section-2
     */
    case NXRRSET = 8;

    /**
     * 服务器不权威
     * 服务器对请求的区域不具有权威性
     * @see https://datatracker.ietf.org/doc/html/rfc2136#section-2
     */
    case NOTAUTH = 9;

    /**
     * 名称不在区域内
     * 动态更新时，指定的名称不在区域内
     * @see https://datatracker.ietf.org/doc/html/rfc2136#section-2
     */
    case NOTZONE = 10;

    /**
     * 错误的EDNS版本
     * 服务器不支持请求中指定的EDNS版本
     * @see https://datatracker.ietf.org/doc/html/rfc6891#section-9
     */
    case BADVERS = 16;

    /**
     * TSIG签名验证失败
     * TSIG签名验证失败或格式错误
     * @see https://datatracker.ietf.org/doc/html/rfc2845#section-4
     * @deprecated 由于与 BADVERS(16) 值冲突，此处使用 17 作为替代值
     */
    case BADSIG = 17;

    /**
     * 密钥不被识别
     * 服务器不认识所使用的密钥
     * @see https://datatracker.ietf.org/doc/html/rfc2845#section-4
     */
    case BADKEY = 18;

    /**
     * 签名时间超出窗口
     * TSIG签名的时间戳超出可接受范围
     * @see https://datatracker.ietf.org/doc/html/rfc2845#section-4
     */
    case BADTIME = 19;

    /**
     * 错误的TKEY模式
     * 不支持请求的TKEY模式
     * @see https://datatracker.ietf.org/doc/html/rfc2930#section-2.6
     */
    case BADMODE = 20;

    /**
     * 重复的密钥名称
     * TKEY协商时发现重复的密钥名称
     * @see https://datatracker.ietf.org/doc/html/rfc2930#section-2.6
     */
    case BADNAME = 21;

    /**
     * 算法不支持
     * 不支持请求的加密算法
     * @see https://datatracker.ietf.org/doc/html/rfc2930#section-2.6
     */
    case BADALG = 22;

    /**
     * 错误的截断
     * 消息截断错误
     * @see https://datatracker.ietf.org/doc/html/rfc4635#section-3.1
     */
    case BADTRUNC = 23;

    /**
     * 错误的Cookie
     * DNS Cookie验证失败
     * @see https://datatracker.ietf.org/doc/html/rfc7873#section-5
     */
    case BADCOOKIE = 24;

    /**
     * 获取响应码的描述
     *
     * @return string 返回当前响应码的中文描述
     */
    public function getDescription(): string
    {
        return match($this) {
            self::NOERROR => '没有错误',
            self::FORMERR => '格式错误',
            self::SERVFAIL => '服务器失败',
            self::NXDOMAIN => '不存在的域名',
            self::NOTIMP => '未实现',
            self::REFUSED => '拒绝查询',
            self::YXDOMAIN => '域名存在但不应该',
            self::YXRRSET => 'RR集合存在但不应该',
            self::NXRRSET => 'RR集合不存在',
            self::NOTAUTH => '服务器不权威',
            self::NOTZONE => '名称不在区域内',
            self::BADVERS, self::BADSIG => 'EDNS版本错误/TSIG签名验证失败',
            self::BADKEY => '密钥不被识别',
            self::BADTIME => '签名时间超出窗口',
            self::BADMODE => '错误的TKEY模式',
            self::BADNAME => '重复的密钥名称',
            self::BADALG => '算法不支持',
            self::BADTRUNC => '错误的截断',
            self::BADCOOKIE => '错误的Cookie',
        };
    }

    /**
     * 判断响应是否成功
     */
    public function isSuccess(): bool
    {
        return $this === self::NOERROR;
    }

    /**
     * 判断响应是否表示域名不存在
     */
    public function isDomainNotExist(): bool
    {
        return $this === self::NXDOMAIN;
    }
}
