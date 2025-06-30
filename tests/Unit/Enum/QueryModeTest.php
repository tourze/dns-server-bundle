<?php

declare(strict_types=1);

namespace DnsServerBundle\Tests\Unit\Enum;

use DnsServerBundle\Enum\QueryMode;
use PHPUnit\Framework\TestCase;

class QueryModeTest extends TestCase
{
    public function testEnumValues(): void
    {
        $this->assertSame('recursive', QueryMode::RECURSIVE->value);
        $this->assertSame('iterative', QueryMode::ITERATIVE->value);
    }

    public function testGetDescription(): void
    {
        $this->assertSame('递归查询', QueryMode::RECURSIVE->getDescription());
        $this->assertSame('迭代查询', QueryMode::ITERATIVE->getDescription());
    }

    public function testGetLabel(): void
    {
        $this->assertSame('递归查询', QueryMode::RECURSIVE->getLabel());
        $this->assertSame('迭代查询', QueryMode::ITERATIVE->getLabel());
    }

    public function testIsRecursive(): void
    {
        $this->assertTrue(QueryMode::RECURSIVE->isRecursive());
        $this->assertFalse(QueryMode::ITERATIVE->isRecursive());
    }

    public function testIsIterative(): void
    {
        $this->assertFalse(QueryMode::RECURSIVE->isIterative());
        $this->assertTrue(QueryMode::ITERATIVE->isIterative());
    }
}