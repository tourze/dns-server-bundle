<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Unit\Enum;

use DnsServerBundle\Enum\ForwardPolicy;
use PHPUnit\Framework\TestCase;

class ForwardPolicyTest extends TestCase
{
    public function testEnumValues(): void
    {
        $this->assertSame('never', ForwardPolicy::NEVER->value);
        $this->assertSame('first', ForwardPolicy::FIRST->value);
        $this->assertSame('only', ForwardPolicy::ONLY->value);
        $this->assertSame('conditional', ForwardPolicy::CONDITIONAL->value);
    }

    public function testGetDescription(): void
    {
        $this->assertSame('从不转发', ForwardPolicy::NEVER->getDescription());
        $this->assertSame('先查本地，找不到再转发', ForwardPolicy::FIRST->getDescription());
        $this->assertSame('只转发，不查本地', ForwardPolicy::ONLY->getDescription());
        $this->assertSame('条件转发（基于域名或记录类型）', ForwardPolicy::CONDITIONAL->getDescription());
    }

    public function testGetLabel(): void
    {
        $this->assertSame('从不转发', ForwardPolicy::NEVER->getLabel());
        $this->assertSame('先查本地，找不到再转发', ForwardPolicy::FIRST->getLabel());
        $this->assertSame('只转发，不查本地', ForwardPolicy::ONLY->getLabel());
        $this->assertSame('条件转发（基于域名或记录类型）', ForwardPolicy::CONDITIONAL->getLabel());
    }

    public function testShouldQueryLocal(): void
    {
        $this->assertTrue(ForwardPolicy::NEVER->shouldQueryLocal());
        $this->assertTrue(ForwardPolicy::FIRST->shouldQueryLocal());
        $this->assertFalse(ForwardPolicy::ONLY->shouldQueryLocal());
        $this->assertTrue(ForwardPolicy::CONDITIONAL->shouldQueryLocal());
    }

    public function testShouldForward(): void
    {
        $this->assertFalse(ForwardPolicy::NEVER->shouldForward());
        $this->assertTrue(ForwardPolicy::FIRST->shouldForward());
        $this->assertTrue(ForwardPolicy::ONLY->shouldForward());
        $this->assertTrue(ForwardPolicy::CONDITIONAL->shouldForward());
    }
}