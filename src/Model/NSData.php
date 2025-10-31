<?php

namespace DnsServerBundle\Model;

final class NSData extends DataAbstract implements \Stringable
{
    public function __construct(private Hostname $target)
    {
    }

    public function __toString(): string
    {
        return (string) $this->target;
    }

    public function getTarget(): Hostname
    {
        return $this->target;
    }

    public function toArray(): array
    {
        return [
            'target' => (string) $this->target,
        ];
    }

    /** @param array<string, mixed> $unserialized */
    public function __unserialize(array $unserialized): void
    {
        $rawTarget = $unserialized['target'] ?? '';
        $this->target = new Hostname(is_string($rawTarget) ? $rawTarget : '');
    }
}
