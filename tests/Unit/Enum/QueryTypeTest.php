<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Unit\Enum;

use DnsServerBundle\Enum\QueryType;
use PHPUnit\Framework\TestCase;

class QueryTypeTest extends TestCase
{
    public function testEnumValues(): void
    {
        $this->assertSame(0, QueryType::STANDARD->value);
        $this->assertSame(1, QueryType::INVERSE->value);
        $this->assertSame(2, QueryType::SERVER_STATUS->value);
        $this->assertSame(4, QueryType::NOTIFY->value);
        $this->assertSame(5, QueryType::UPDATE->value);
    }

    public function testGetDescription(): void
    {
        $this->assertSame('标准查询', QueryType::STANDARD->getDescription());
        $this->assertSame('反向查询', QueryType::INVERSE->getDescription());
        $this->assertSame('服务器状态请求', QueryType::SERVER_STATUS->getDescription());
        $this->assertSame('区域变更通知', QueryType::NOTIFY->getDescription());
        $this->assertSame('动态更新请求', QueryType::UPDATE->getDescription());
    }

    public function testGetLabel(): void
    {
        $this->assertSame('标准查询', QueryType::STANDARD->getLabel());
        $this->assertSame('反向查询', QueryType::INVERSE->getLabel());
        $this->assertSame('服务器状态请求', QueryType::SERVER_STATUS->getLabel());
        $this->assertSame('区域变更通知', QueryType::NOTIFY->getLabel());
        $this->assertSame('动态更新请求', QueryType::UPDATE->getLabel());
    }
}