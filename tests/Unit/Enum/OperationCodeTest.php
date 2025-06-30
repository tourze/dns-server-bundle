<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Unit\Enum;

use DnsServerBundle\Enum\OperationCode;
use PHPUnit\Framework\TestCase;

class OperationCodeTest extends TestCase
{
    public function testEnumValues(): void
    {
        $this->assertSame(0, OperationCode::QUERY->value);
        $this->assertSame(1, OperationCode::IQUERY->value);
        $this->assertSame(2, OperationCode::STATUS->value);
        $this->assertSame(4, OperationCode::NOTIFY->value);
        $this->assertSame(5, OperationCode::UPDATE->value);
    }

    public function testGetDescription(): void
    {
        $this->assertSame('标准查询', OperationCode::QUERY->getDescription());
        $this->assertSame('反向查询', OperationCode::IQUERY->getDescription());
        $this->assertSame('服务器状态请求', OperationCode::STATUS->getDescription());
        $this->assertSame('通知', OperationCode::NOTIFY->getDescription());
        $this->assertSame('动态更新', OperationCode::UPDATE->getDescription());
    }

    public function testGetLabel(): void
    {
        $this->assertSame('标准查询', OperationCode::QUERY->getLabel());
        $this->assertSame('反向查询', OperationCode::IQUERY->getLabel());
        $this->assertSame('服务器状态请求', OperationCode::STATUS->getLabel());
        $this->assertSame('通知', OperationCode::NOTIFY->getLabel());
        $this->assertSame('动态更新', OperationCode::UPDATE->getLabel());
    }
}