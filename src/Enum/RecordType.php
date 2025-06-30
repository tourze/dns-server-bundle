<?php

declare(strict_types=1);

namespace DnsServerBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * DNS 资源记录类型枚举
 *
 * 定义了 DNS 中所有标准的资源记录类型。
 * 每种记录类型都有其特定的用途和数据格式。
 *
 * @see https://datatracker.ietf.org/doc/html/rfc1035#section-3.2.2 基础记录类型
 * @see https://www.iana.org/assignments/dns-parameters/dns-parameters.xhtml#dns-parameters-4 IANA DNS记录类型列表
 */
enum RecordType: int implements Itemable, Labelable, Selectable
{
    use ItemTrait;
    use SelectTrait;
    /**
     * IPv4 地址记录
     * 将域名映射到 IPv4 地址
     * @see https://datatracker.ietf.org/doc/html/rfc1035#section-3.4.1
     */
    case A = 1;

    /**
     * 名称服务器记录
     * 指定域的权威名称服务器
     * @see https://datatracker.ietf.org/doc/html/rfc1035#section-3.3.11
     */
    case NS = 2;

    /**
     * 规范名称记录
     * 创建域名别名，将一个域名指向另一个域名
     * @see https://datatracker.ietf.org/doc/html/rfc1035#section-3.3.1
     */
    case CNAME = 5;

    /**
     * 权威记录起始
     * 指定区域的权威信息，包含主域名服务器、管理员、序列号等
     * @see https://datatracker.ietf.org/doc/html/rfc1035#section-3.3.13
     */
    case SOA = 6;

    /**
     * 指针记录
     * 用于反向DNS查找，将IP地址映射到域名
     * @see https://datatracker.ietf.org/doc/html/rfc1035#section-3.3.12
     */
    case PTR = 12;

    /**
     * 邮件交换记录
     * 指定负责接收邮件的服务器
     * @see https://datatracker.ietf.org/doc/html/rfc1035#section-3.3.9
     */
    case MX = 15;

    /**
     * 文本记录
     * 存储文本信息，通常用于SPF、DKIM等
     * @see https://datatracker.ietf.org/doc/html/rfc1035#section-3.3.14
     */
    case TXT = 16;

    /**
     * IPv6 地址记录
     * 将域名映射到 IPv6 地址
     * @see https://datatracker.ietf.org/doc/html/rfc3596#section-2.1
     */
    case AAAA = 28;

    /**
     * 服务定位记录
     * 用于定义服务的位置，包括主机名、端口等
     * @see https://datatracker.ietf.org/doc/html/rfc2782
     */
    case SRV = 33;

    /**
     * 命名权威指针
     * 用于服务发现和URI映射
     * @see https://datatracker.ietf.org/doc/html/rfc2915
     */
    case NAPTR = 35;

    /**
     * EDNS0 选项
     * 扩展DNS消息大小限制等功能
     * @see https://datatracker.ietf.org/doc/html/rfc6891
     */
    case OPT = 41;

    /**
     * 委托签名者
     * DNSSEC中用于验证区域委托的安全性
     * @see https://datatracker.ietf.org/doc/html/rfc4034#section-5
     */
    case DS = 43;

    /**
     * DNSSEC 签名
     * 包含资源记录集的数字签名
     * @see https://datatracker.ietf.org/doc/html/rfc4034#section-3
     */
    case RRSIG = 46;

    /**
     * 下一个安全记录
     * DNSSEC中用于证明记录不存在
     * @see https://datatracker.ietf.org/doc/html/rfc4034#section-4
     */
    case NSEC = 47;

    /**
     * DNS 密钥记录
     * 存储用于DNSSEC的公钥
     * @see https://datatracker.ietf.org/doc/html/rfc4034#section-2
     */
    case DNSKEY = 48;

    /**
     * NSEC 版本 3
     * NSEC的改进版本，提供更好的隐私保护
     * @see https://datatracker.ietf.org/doc/html/rfc5155
     */
    case NSEC3 = 50;

    /**
     * NSEC3 参数
     * 定义NSEC3记录的创建参数
     * @see https://datatracker.ietf.org/doc/html/rfc5155
     */
    case NSEC3PARAM = 51;

    /**
     * DANE TLSA 记录
     * 用于在DNS中发布TLS证书信息
     * @see https://datatracker.ietf.org/doc/html/rfc6698
     */
    case TLSA = 52;

    /**
     * 证书颁发机构授权
     * 指定哪些CA被授权为域名颁发证书
     * @see https://datatracker.ietf.org/doc/html/rfc6844
     */
    case CAA = 257;

    /**
     * 域名别名
     * 创建域名别名，将一个域名指向另一个域名
     * @see https://datatracker.ietf.org/doc/html/rfc1035#section-3.3.1
     */
    case DNAME = 39;

    /**
     * 请求区域传输
     * 请求区域传输
     * @see https://datatracker.ietf.org/doc/html/rfc1035#section-3.2.3
     */
    case AXFR = 252;

    /**
     * 请求所有记录
     * 请求所有记录
     * @see https://datatracker.ietf.org/doc/html/rfc1035#section-3.2.3
     */
    case ANY = 255;

    /**
     * 获取记录类型的名称
     *
     * @return string 返回记录类型的标准名称
     */
    public function getName(): string
    {
        return match($this) {
            self::A => 'A',
            self::NS => 'NS',
            self::CNAME => 'CNAME',
            self::SOA => 'SOA',
            self::PTR => 'PTR',
            self::MX => 'MX',
            self::TXT => 'TXT',
            self::AAAA => 'AAAA',
            self::SRV => 'SRV',
            self::NAPTR => 'NAPTR',
            self::OPT => 'OPT',
            self::DS => 'DS',
            self::RRSIG => 'RRSIG',
            self::NSEC => 'NSEC',
            self::DNSKEY => 'DNSKEY',
            self::NSEC3 => 'NSEC3',
            self::NSEC3PARAM => 'NSEC3PARAM',
            self::TLSA => 'TLSA',
            self::CAA => 'CAA',
            self::DNAME => 'DNAME',
            self::AXFR => 'AXFR',
            self::ANY => 'ANY',
        };
    }

    /**
     * 根据名称获取记录类型
     */
    public static function fromName(string $name): ?self
    {
        return match(strtoupper($name)) {
            'A' => self::A,
            'NS' => self::NS,
            'CNAME' => self::CNAME,
            'SOA' => self::SOA,
            'PTR' => self::PTR,
            'MX' => self::MX,
            'TXT' => self::TXT,
            'AAAA' => self::AAAA,
            'SRV' => self::SRV,
            'NAPTR' => self::NAPTR,
            'OPT' => self::OPT,
            'DS' => self::DS,
            'RRSIG' => self::RRSIG,
            'NSEC' => self::NSEC,
            'DNSKEY' => self::DNSKEY,
            'NSEC3' => self::NSEC3,
            'NSEC3PARAM' => self::NSEC3PARAM,
            'TLSA' => self::TLSA,
            'CAA' => self::CAA,
            'DNAME' => self::DNAME,
            'AXFR' => self::AXFR,
            'ANY' => self::ANY,
            default => null,
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::A => 'IPv4地址',
            self::NS => '域名服务器',
            self::CNAME => '规范名称',
            self::SOA => '权威记录的起始',
            self::PTR => '指针记录',
            self::MX => '邮件交换',
            self::TXT => '文本记录',
            self::AAAA => 'IPv6地址',
            self::SRV => '服务定位',
            self::NAPTR => '命名权威指针',
            self::OPT => 'EDNS0选项',
            self::DS => '委托签名者',
            self::RRSIG => 'DNSSEC签名',
            self::NSEC => '下一个安全记录',
            self::DNSKEY => 'DNS密钥记录',
            self::NSEC3 => 'NSEC版本3',
            self::NSEC3PARAM => 'NSEC3参数',
            self::TLSA => 'DANE TLSA记录',
            self::CAA => '证书颁发机构授权',
            self::DNAME => '域名别名',
            self::AXFR => '请求区域传输',
            self::ANY => '请求所有记录',
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
