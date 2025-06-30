<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Unit\Enum;

use DnsServerBundle\Enum\SecurityPolicy;
use PHPUnit\Framework\TestCase;

class SecurityPolicyTest extends TestCase
{
    public function testEnumValues(): void
    {
        $this->assertSame('open', SecurityPolicy::OPEN->value);
        $this->assertSame('restricted', SecurityPolicy::RESTRICTED->value);
        $this->assertSame('dnssec', SecurityPolicy::DNSSEC->value);
        $this->assertSame('strict', SecurityPolicy::STRICT->value);
    }

    public function testGetDescription(): void
    {
        $this->assertSame('开放模式，接受所有请求', SecurityPolicy::OPEN->getDescription());
        $this->assertSame('限制模式，只接受特定来源的请求', SecurityPolicy::RESTRICTED->getDescription());
        $this->assertSame('DNSSEC模式，支持DNSSEC验证', SecurityPolicy::DNSSEC->getDescription());
        $this->assertSame('严格模式，完全验证所有请求', SecurityPolicy::STRICT->getDescription());
    }

    public function testGetLabel(): void
    {
        $this->assertSame('开放模式，接受所有请求', SecurityPolicy::OPEN->getLabel());
        $this->assertSame('限制模式，只接受特定来源的请求', SecurityPolicy::RESTRICTED->getLabel());
        $this->assertSame('DNSSEC模式，支持DNSSEC验证', SecurityPolicy::DNSSEC->getLabel());
        $this->assertSame('严格模式，完全验证所有请求', SecurityPolicy::STRICT->getLabel());
    }

    public function testShouldValidateSource(): void
    {
        $this->assertFalse(SecurityPolicy::OPEN->shouldValidateSource());
        $this->assertTrue(SecurityPolicy::RESTRICTED->shouldValidateSource());
        $this->assertFalse(SecurityPolicy::DNSSEC->shouldValidateSource());
        $this->assertTrue(SecurityPolicy::STRICT->shouldValidateSource());
    }

    public function testRequiresDnssec(): void
    {
        $this->assertFalse(SecurityPolicy::OPEN->requiresDnssec());
        $this->assertFalse(SecurityPolicy::RESTRICTED->requiresDnssec());
        $this->assertTrue(SecurityPolicy::DNSSEC->requiresDnssec());
        $this->assertTrue(SecurityPolicy::STRICT->requiresDnssec());
    }
}