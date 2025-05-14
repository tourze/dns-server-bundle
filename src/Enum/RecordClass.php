<?php

namespace DnsServerBundle\Enum;

/**
 * DNS 记录类枚举
 * 
 * 定义了 DNS 资源记录的类别。
 * 类别字段在 DNS 消息中用于区分不同网络类型的记录。
 * 
 * @see https://datatracker.ietf.org/doc/html/rfc1035#section-3.2.4 记录类定义
 * @see https://www.iana.org/assignments/dns-parameters/dns-parameters.xhtml#dns-parameters-2 IANA DNS CLASS 列表
 */
enum RecordClass: int
{
    /**
     * 互联网类
     * 最常用的记录类，用于互联网DNS记录
     * @see https://datatracker.ietf.org/doc/html/rfc1035#section-3.2.4
     */
    case IN = 1;          // 互联网

    /**
     * CSNET类
     * 用于CSNET网络（已废弃）
     * @deprecated
     */
    case CS = 2;          // CSNET类 (已废弃)

    /**
     * CHAOS类
     * 用于MIT Chaos网络
     * @see https://datatracker.ietf.org/doc/html/rfc973
     */
    case CH = 3;          // CHAOS类

    /**
     * Hesiod类
     * MIT Athena项目使用的名称服务
     * @see https://datatracker.ietf.org/doc/html/rfc1035#section-3.2.4
     */
    case HS = 4;          // Hesiod类

    /**
     * 任意类
     * 用于查询时匹配任意记录类
     * @see https://datatracker.ietf.org/doc/html/rfc1035#section-3.2.4
     */
    case ANY = 255;       // 任意类

    /**
     * 获取记录类的名称
     * 
     * @return string 返回记录类的标准名称
     */
    public function getName(): string
    {
        return match($this) {
            self::IN => 'IN',
            self::CS => 'CS',
            self::CH => 'CH',
            self::HS => 'HS',
            self::ANY => 'ANY',
        };
    }

    /**
     * 获取记录类的描述
     * 
     * @return string 返回记录类的中文描述
     */
    public function getDescription(): string
    {
        return match($this) {
            self::IN => '互联网',
            self::CS => 'CSNET类 (已废弃)',
            self::CH => 'CHAOS类',
            self::HS => 'Hesiod类',
            self::ANY => '任意类',
        };
    }

    /**
     * 根据名称获取记录类
     *
     * @param string $name 记录类的名称
     * @return self|null 返回对应的记录类枚举值，如果不存在则返回null
     */
    public static function fromName(string $name): ?self
    {
        return match(strtoupper($name)) {
            'IN' => self::IN,
            'CS' => self::CS,
            'CH' => self::CH,
            'HS' => self::HS,
            'ANY' => self::ANY,
            default => null,
        };
    }
}
