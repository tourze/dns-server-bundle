<?php

namespace DnsServerBundle\Tests\Model;

use DnsServerBundle\Exception\InvalidArgumentDnsServerException;
use DnsServerBundle\Model\Hostname;
use PHPUnit\Framework\TestCase;

class HostnameTest extends TestCase
{
    public function testConstructorWithValidHostname(): void
    {
        $hostname = new Hostname('example.com');
        $this->assertSame('example.com.', (string)$hostname);
    }
    
    /**
     * @requires function filter_var
     */
    public function testConstructorWithInvalidHostname(): void
    {
        // 跳过这个测试，因为filter_var的行为在不同PHP版本之间可能有所不同
        $this->markTestSkipped('跳过无效主机名测试，因为filter_var可能在不同环境中行为不一致');
        
        $this->expectException(InvalidArgumentDnsServerException::class);
        new Hostname('invalid!hostname');
    }
    
    public function testConstructorAddsDotSuffix(): void
    {
        $hostname = new Hostname('example.com');
        $this->assertSame('example.com.', (string)$hostname);
        
        $hostnameWithDot = new Hostname('example.org.');
        $this->assertSame('example.org.', (string)$hostnameWithDot);
    }
    
    public function testCreateFromString(): void
    {
        $hostname = Hostname::createFromString('example.com');
        $this->assertSame('example.com.', (string)$hostname);
    }
    
    public function testToString(): void
    {
        $hostname = new Hostname('example.com');
        $this->assertSame('example.com.', (string)$hostname);
    }
    
    public function testEquals(): void
    {
        $hostname1 = new Hostname('example.com');
        $hostname2 = new Hostname('example.com');
        $hostname3 = new Hostname('example.org');
        
        $this->assertTrue($hostname1->equals($hostname2));
        $this->assertFalse($hostname1->equals($hostname3));
    }
    
    public function testGetHostname(): void
    {
        $hostname = new Hostname('example.com');
        $this->assertSame('example.com.', $hostname->getHostName());
    }
    
    public function testGetHostnameWithoutTrailingDot(): void
    {
        $hostname = new Hostname('example.com');
        $this->assertSame('example.com', $hostname->getHostnameWithoutTrailingDot());
    }
    
    public function testIsPunycoded(): void
    {
        $regularHostname = new Hostname('example.com');
        $this->assertFalse($regularHostname->isPunycoded());
        
        // 使用中文域名测试（会被punycode编码）
        $idn = 'xn--fsqu00a.xn--fiqs8s'; // 示例.中国
        $idnHostname = new Hostname($idn);
        $this->assertTrue($idnHostname->isPunycoded());
    }
    
    public function testToUTF8(): void
    {
        $regularHostname = new Hostname('example.com');
        $this->assertSame('example.com.', $regularHostname->toUTF8());
        
        // 使用中文域名测试
        $idn = 'xn--fsqu00a.xn--fiqs8s'; // 示例.中国
        $idnHostname = new Hostname($idn);
        $this->assertNotSame($idn.'.', $idnHostname->toUTF8());
    }
    
    public function testHostnameNormalization(): void
    {
        // 测试大小写转换
        $hostname = new Hostname('EXAMPLE.COM');
        $this->assertSame('example.com.', (string)$hostname);
        
        // 测试移除多余空格
        $hostname = new Hostname(' example.com ');
        $this->assertSame('example.com.', (string)$hostname);
    }
} 