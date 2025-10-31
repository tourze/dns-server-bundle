<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Enum;

use DnsServerBundle\Enum\ResponseCode;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(ResponseCode::class)]
final class ResponseCodeTest extends AbstractEnumTestCase
{
    public function testEnumValues(): void
    {
        $this->assertSame(0, ResponseCode::NOERROR->value);
        $this->assertSame(1, ResponseCode::FORMERR->value);
        $this->assertSame(2, ResponseCode::SERVFAIL->value);
        $this->assertSame(3, ResponseCode::NXDOMAIN->value);
        $this->assertSame(4, ResponseCode::NOTIMP->value);
        $this->assertSame(5, ResponseCode::REFUSED->value);
        $this->assertSame(6, ResponseCode::YXDOMAIN->value);
        $this->assertSame(7, ResponseCode::YXRRSET->value);
        $this->assertSame(8, ResponseCode::NXRRSET->value);
        $this->assertSame(9, ResponseCode::NOTAUTH->value);
        $this->assertSame(10, ResponseCode::NOTZONE->value);
        $this->assertSame(16, ResponseCode::BADVERS->value);
        $this->assertSame(17, ResponseCode::BADSIG->value);
        $this->assertSame(18, ResponseCode::BADKEY->value);
        $this->assertSame(19, ResponseCode::BADTIME->value);
        $this->assertSame(20, ResponseCode::BADMODE->value);
        $this->assertSame(21, ResponseCode::BADNAME->value);
        $this->assertSame(22, ResponseCode::BADALG->value);
        $this->assertSame(23, ResponseCode::BADTRUNC->value);
        $this->assertSame(24, ResponseCode::BADCOOKIE->value);
    }

    public function testGetDescription(): void
    {
        $this->assertSame('没有错误', ResponseCode::NOERROR->getDescription());
        $this->assertSame('格式错误', ResponseCode::FORMERR->getDescription());
        $this->assertSame('服务器失败', ResponseCode::SERVFAIL->getDescription());
        $this->assertSame('不存在的域名', ResponseCode::NXDOMAIN->getDescription());
        $this->assertSame('未实现', ResponseCode::NOTIMP->getDescription());
        $this->assertSame('拒绝查询', ResponseCode::REFUSED->getDescription());
        $this->assertSame('域名存在但不应该', ResponseCode::YXDOMAIN->getDescription());
        $this->assertSame('RR集合存在但不应该', ResponseCode::YXRRSET->getDescription());
        $this->assertSame('RR集合不存在', ResponseCode::NXRRSET->getDescription());
        $this->assertSame('服务器不权威', ResponseCode::NOTAUTH->getDescription());
        $this->assertSame('名称不在区域内', ResponseCode::NOTZONE->getDescription());
        $this->assertSame('EDNS版本错误/TSIG签名验证失败', ResponseCode::BADVERS->getDescription());
        $this->assertSame('EDNS版本错误/TSIG签名验证失败', ResponseCode::BADSIG->getDescription());
        $this->assertSame('密钥不被识别', ResponseCode::BADKEY->getDescription());
        $this->assertSame('签名时间超出窗口', ResponseCode::BADTIME->getDescription());
        $this->assertSame('错误的TKEY模式', ResponseCode::BADMODE->getDescription());
        $this->assertSame('重复的密钥名称', ResponseCode::BADNAME->getDescription());
        $this->assertSame('算法不支持', ResponseCode::BADALG->getDescription());
        $this->assertSame('错误的截断', ResponseCode::BADTRUNC->getDescription());
        $this->assertSame('错误的Cookie', ResponseCode::BADCOOKIE->getDescription());
    }

    public function testGetLabel(): void
    {
        $this->assertSame('没有错误', ResponseCode::NOERROR->getLabel());
        $this->assertSame('格式错误', ResponseCode::FORMERR->getLabel());
        $this->assertSame('服务器失败', ResponseCode::SERVFAIL->getLabel());
        $this->assertSame('不存在的域名', ResponseCode::NXDOMAIN->getLabel());
    }

    public function testIsSuccess(): void
    {
        $this->assertTrue(ResponseCode::NOERROR->isSuccess());
        $this->assertFalse(ResponseCode::FORMERR->isSuccess());
        $this->assertFalse(ResponseCode::SERVFAIL->isSuccess());
        $this->assertFalse(ResponseCode::NXDOMAIN->isSuccess());
        $this->assertFalse(ResponseCode::REFUSED->isSuccess());
    }

    public function testIsDomainNotExist(): void
    {
        $this->assertFalse(ResponseCode::NOERROR->isDomainNotExist());
        $this->assertFalse(ResponseCode::FORMERR->isDomainNotExist());
        $this->assertFalse(ResponseCode::SERVFAIL->isDomainNotExist());
        $this->assertTrue(ResponseCode::NXDOMAIN->isDomainNotExist());
        $this->assertFalse(ResponseCode::REFUSED->isDomainNotExist());
    }

    public function testToArray(): void
    {
        // toArray 是实例方法，返回单个枚举项的数组表示
        $array = ResponseCode::NOERROR->toArray();

        $this->assertIsArray($array);
        $this->assertCount(2, $array);

        // 验证数组的键值对
        $this->assertArrayHasKey('value', $array);
        $this->assertArrayHasKey('label', $array);

        // 验证值是否正确
        $this->assertSame(0, $array['value']);
        $this->assertSame('没有错误', $array['label']);

        // 测试其他枚举值
        $fORMERRArray = ResponseCode::FORMERR->toArray();
        $this->assertSame(1, $fORMERRArray['value']);
        $this->assertSame('格式错误', $fORMERRArray['label']);
    }
}
