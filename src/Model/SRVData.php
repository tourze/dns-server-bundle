<?php

namespace DnsServerBundle\Model;

final class SRVData extends DataAbstract implements \Stringable
{
    public function __construct(private int $priority, private int $weight, private int $port, private Hostname $target)
    {
    }

    public function __toString(): string
    {
        return "{$this->priority} {$this->weight} {$this->port} {$this->target}";
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function getWeight(): int
    {
        return $this->weight;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function getTarget(): Hostname
    {
        return $this->target;
    }

    public function toArray(): array
    {
        return [
            'priority' => $this->priority,
            'weight' => $this->weight,
            'port' => $this->port,
            'target' => (string) $this->target,
        ];
    }

    /** @param array<string, mixed> $unserialized */
    public function __unserialize(array $unserialized): void
    {
        $rawPriority = $unserialized['priority'] ?? 0;
        $rawWeight = $unserialized['weight'] ?? 0;
        $rawPort = $unserialized['port'] ?? 0;
        $rawTarget = $unserialized['target'] ?? '';

        $this->priority = is_int($rawPriority) ? $rawPriority : (is_numeric($rawPriority) ? (int) $rawPriority : 0);
        $this->weight = is_int($rawWeight) ? $rawWeight : (is_numeric($rawWeight) ? (int) $rawWeight : 0);
        $this->port = is_int($rawPort) ? $rawPort : (is_numeric($rawPort) ? (int) $rawPort : 0);
        $this->target = new Hostname(is_string($rawTarget) ? $rawTarget : '');
    }
}
