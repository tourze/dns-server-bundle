<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Enum;

use DnsServerBundle\Enum\RecordType;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(RecordType::class)]
final class RecordTypeTest extends AbstractEnumTestCase
{
    public function testEnumValues(): void
    {
        // 测试枚举值是否正确定义
        $this->assertSame(1, RecordType::A->value);
        $this->assertSame(2, RecordType::NS->value);
        $this->assertSame(5, RecordType::CNAME->value);
        $this->assertSame(6, RecordType::SOA->value);
        $this->assertSame(12, RecordType::PTR->value);
        $this->assertSame(15, RecordType::MX->value);
        $this->assertSame(16, RecordType::TXT->value);
        $this->assertSame(28, RecordType::AAAA->value);
        $this->assertSame(33, RecordType::SRV->value);
        $this->assertSame(35, RecordType::NAPTR->value);
        $this->assertSame(41, RecordType::OPT->value);
        $this->assertSame(43, RecordType::DS->value);
        $this->assertSame(46, RecordType::RRSIG->value);
        $this->assertSame(47, RecordType::NSEC->value);
        $this->assertSame(48, RecordType::DNSKEY->value);
        $this->assertSame(50, RecordType::NSEC3->value);
        $this->assertSame(51, RecordType::NSEC3PARAM->value);
        $this->assertSame(52, RecordType::TLSA->value);
        $this->assertSame(257, RecordType::CAA->value);
        $this->assertSame(39, RecordType::DNAME->value);
        $this->assertSame(252, RecordType::AXFR->value);
        $this->assertSame(255, RecordType::ANY->value);
    }

    public function testGetName(): void
    {
        // 测试getName方法返回值是否正确
        $this->assertSame('A', RecordType::A->getName());
        $this->assertSame('NS', RecordType::NS->getName());
        $this->assertSame('CNAME', RecordType::CNAME->getName());
        $this->assertSame('SOA', RecordType::SOA->getName());
        $this->assertSame('PTR', RecordType::PTR->getName());
        $this->assertSame('MX', RecordType::MX->getName());
        $this->assertSame('TXT', RecordType::TXT->getName());
        $this->assertSame('AAAA', RecordType::AAAA->getName());
        $this->assertSame('SRV', RecordType::SRV->getName());
        $this->assertSame('NAPTR', RecordType::NAPTR->getName());
        $this->assertSame('OPT', RecordType::OPT->getName());
        $this->assertSame('DS', RecordType::DS->getName());
        $this->assertSame('RRSIG', RecordType::RRSIG->getName());
        $this->assertSame('NSEC', RecordType::NSEC->getName());
        $this->assertSame('DNSKEY', RecordType::DNSKEY->getName());
        $this->assertSame('NSEC3', RecordType::NSEC3->getName());
        $this->assertSame('NSEC3PARAM', RecordType::NSEC3PARAM->getName());
        $this->assertSame('TLSA', RecordType::TLSA->getName());
        $this->assertSame('CAA', RecordType::CAA->getName());
        $this->assertSame('DNAME', RecordType::DNAME->getName());
        $this->assertSame('AXFR', RecordType::AXFR->getName());
        $this->assertSame('ANY', RecordType::ANY->getName());
    }

    public function testFromName(): void
    {
        // 测试fromName方法能正确识别记录类型
        $this->assertSame(RecordType::A, RecordType::fromName('A'));
        $this->assertSame(RecordType::NS, RecordType::fromName('NS'));
        $this->assertSame(RecordType::CNAME, RecordType::fromName('CNAME'));
        $this->assertSame(RecordType::SOA, RecordType::fromName('SOA'));
        $this->assertSame(RecordType::PTR, RecordType::fromName('PTR'));
        $this->assertSame(RecordType::MX, RecordType::fromName('MX'));
        $this->assertSame(RecordType::TXT, RecordType::fromName('TXT'));
        $this->assertSame(RecordType::AAAA, RecordType::fromName('AAAA'));
        $this->assertSame(RecordType::SRV, RecordType::fromName('SRV'));
        $this->assertSame(RecordType::NAPTR, RecordType::fromName('NAPTR'));
        $this->assertSame(RecordType::OPT, RecordType::fromName('OPT'));
        $this->assertSame(RecordType::DS, RecordType::fromName('DS'));
        $this->assertSame(RecordType::RRSIG, RecordType::fromName('RRSIG'));
        $this->assertSame(RecordType::NSEC, RecordType::fromName('NSEC'));
        $this->assertSame(RecordType::DNSKEY, RecordType::fromName('DNSKEY'));
        $this->assertSame(RecordType::NSEC3, RecordType::fromName('NSEC3'));
        $this->assertSame(RecordType::NSEC3PARAM, RecordType::fromName('NSEC3PARAM'));
        $this->assertSame(RecordType::TLSA, RecordType::fromName('TLSA'));
        $this->assertSame(RecordType::CAA, RecordType::fromName('CAA'));
        $this->assertSame(RecordType::DNAME, RecordType::fromName('DNAME'));
        $this->assertSame(RecordType::AXFR, RecordType::fromName('AXFR'));
        $this->assertSame(RecordType::ANY, RecordType::fromName('ANY'));
    }

    public function testFromNameWithLowercase(): void
    {
        // 测试fromName方法对大小写不敏感
        $this->assertSame(RecordType::A, RecordType::fromName('a'));
        $this->assertSame(RecordType::NS, RecordType::fromName('ns'));
        $this->assertSame(RecordType::CNAME, RecordType::fromName('cname'));
        $this->assertSame(RecordType::AAAA, RecordType::fromName('aaaa'));
        $this->assertSame(RecordType::MX, RecordType::fromName('mx'));
    }

    public function testFromNameWithInvalidNameReturnsNull(): void
    {
        // 测试fromName方法处理无效名称的情况
        $this->assertNull(RecordType::fromName('INVALID'));
        $this->assertNull(RecordType::fromName(''));
        $this->assertNull(RecordType::fromName('123'));
    }

    public function testGetDescription(): void
    {
        // 测试getDescription方法返回值是否正确
        $this->assertSame('IPv4地址', RecordType::A->getDescription());
        $this->assertSame('域名服务器', RecordType::NS->getDescription());
        $this->assertSame('规范名称', RecordType::CNAME->getDescription());
        $this->assertSame('权威记录的起始', RecordType::SOA->getDescription());
        $this->assertSame('指针记录', RecordType::PTR->getDescription());
        $this->assertSame('邮件交换', RecordType::MX->getDescription());
        $this->assertSame('文本记录', RecordType::TXT->getDescription());
        $this->assertSame('IPv6地址', RecordType::AAAA->getDescription());
        $this->assertSame('服务定位', RecordType::SRV->getDescription());
        $this->assertSame('命名权威指针', RecordType::NAPTR->getDescription());
        $this->assertSame('EDNS0选项', RecordType::OPT->getDescription());
        $this->assertSame('委托签名者', RecordType::DS->getDescription());
        $this->assertSame('DNSSEC签名', RecordType::RRSIG->getDescription());
        $this->assertSame('下一个安全记录', RecordType::NSEC->getDescription());
        $this->assertSame('DNS密钥记录', RecordType::DNSKEY->getDescription());
        $this->assertSame('NSEC版本3', RecordType::NSEC3->getDescription());
        $this->assertSame('NSEC3参数', RecordType::NSEC3PARAM->getDescription());
        $this->assertSame('DANE TLSA记录', RecordType::TLSA->getDescription());
        $this->assertSame('证书颁发机构授权', RecordType::CAA->getDescription());
        $this->assertSame('域名别名', RecordType::DNAME->getDescription());
        $this->assertSame('请求区域传输', RecordType::AXFR->getDescription());
        $this->assertSame('请求所有记录', RecordType::ANY->getDescription());
    }

    public function testToArray(): void
    {
        // toArray 是实例方法，返回单个枚举项的数组表示
        $array = RecordType::A->toArray();

        $this->assertIsArray($array);
        $this->assertCount(2, $array);

        // 验证数组的键值对
        $this->assertArrayHasKey('value', $array);
        $this->assertArrayHasKey('label', $array);

        // 验证值是否正确
        $this->assertSame(1, $array['value']);
        $this->assertSame('IPv4地址', $array['label']);

        // 测试其他枚举值
        $aAAAArray = RecordType::AAAA->toArray();
        $this->assertSame(28, $aAAAArray['value']);
        $this->assertSame('IPv6地址', $aAAAArray['label']);
    }
}
