<?php

declare(strict_types=1);

namespace DnsServerBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum MatchStrategy: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case EXACT = 'exact';           // 精确匹配
    case WILDCARD = 'wildcard';     // 通配符匹配
    case REGEX = 'regex';           // 正则匹配
    case PREFIX = 'prefix';         // 前缀匹配
    case SUFFIX = 'suffix';         // 后缀匹配

    public function getLabel(): string
    {
        return match ($this) {
            self::EXACT => '精确匹配',
            self::WILDCARD => '通配符匹配',
            self::REGEX => '正则匹配',
            self::PREFIX => '前缀匹配',
            self::SUFFIX => '后缀匹配',
        };
    }
}
