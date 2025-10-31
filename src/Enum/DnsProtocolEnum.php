<?php

declare(strict_types=1);

namespace DnsServerBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum DnsProtocolEnum: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case UDP = 'udp';
    case TCP = 'tcp';
    case DOH = 'doh';
    case DOT = 'dot';

    public function getLabel(): string
    {
        return match ($this) {
            self::UDP => 'UDP',
            self::TCP => 'TCP',
            self::DOH => 'DNS over HTTPS',
            self::DOT => 'DNS over TLS',
        };
    }
}
