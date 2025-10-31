<?php

namespace DnsServerBundle\Model;

final class MXData extends DataAbstract implements \Stringable
{
    public function __construct(private Hostname $target, private int $priority = 0)
    {
    }

    public function __toString(): string
    {
        return "{$this->priority} {$this->target}";
    }

    public function getTarget(): Hostname
    {
        return $this->target;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function toArray(): array
    {
        return [
            'target' => (string) $this->target,
            'priority' => $this->priority,
        ];
    }

    /** @param array<string, mixed> $unserialized */
    public function __unserialize(array $unserialized): void
    {
        $rawTarget = $unserialized['target'] ?? '';
        $rawPriority = $unserialized['priority'] ?? 0;

        $this->target = new Hostname(is_string($rawTarget) ? $rawTarget : '');
        $this->priority = is_int($rawPriority) ? $rawPriority : (is_numeric($rawPriority) ? (int) $rawPriority : 0);
    }
}
