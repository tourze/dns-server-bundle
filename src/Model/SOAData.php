<?php

namespace DnsServerBundle\Model;

final class SOAData extends DataAbstract implements \Stringable
{
    /**
     * @var string
     */
    private const TEMPLATE = '%s %s %s %s %s %s %s';

    public function __construct(
        private Hostname $mname,
        private Hostname $rname,
        private int $serial,
        private int $refresh,
        private int $retry,
        private int $expire,
        private int $minTTL,
    ) {
    }

    public function __toString(): string
    {
        $values = [
            (string) $this->mname,
            (string) $this->rname,
            (string) $this->serial,
            (string) $this->refresh,
            (string) $this->retry,
            (string) $this->expire,
            (string) $this->minTTL,
        ];

        return \vsprintf(self::TEMPLATE, $values);
    }

    public function getMname(): Hostname
    {
        return $this->mname;
    }

    public function getRname(): Hostname
    {
        return $this->rname;
    }

    public function getSerial(): int
    {
        return $this->serial;
    }

    public function getRefresh(): int
    {
        return $this->refresh;
    }

    public function getRetry(): int
    {
        return $this->retry;
    }

    public function getExpire(): int
    {
        return $this->expire;
    }

    public function getMinTTL(): int
    {
        return $this->minTTL;
    }

    public function toArray(): array
    {
        return [
            'mname' => (string) $this->mname,
            'rname' => (string) $this->rname,
            'serial' => $this->serial,
            'refresh' => $this->refresh,
            'retry' => $this->retry,
            'expire' => $this->expire,
            'minimumTTL' => $this->minTTL,
        ];
    }

    /** @param array<string, mixed> $unserialized */
    public function __unserialize(array $unserialized): void
    {
        $this->mname = new Hostname($this->extractString($unserialized, 'mname'));
        $this->rname = new Hostname($this->extractString($unserialized, 'rname'));
        $this->serial = $this->extractInt($unserialized, 'serial');
        $this->refresh = $this->extractInt($unserialized, 'refresh');
        $this->retry = $this->extractInt($unserialized, 'retry');
        $this->expire = $this->extractInt($unserialized, 'expire');
        $this->minTTL = $this->extractInt($unserialized, 'minimumTTL');
    }

    /** @param array<string, mixed> $data */
    private function extractString(array $data, string $key): string
    {
        $value = $data[$key] ?? '';

        return is_string($value) ? $value : '';
    }

    /** @param array<string, mixed> $data */
    private function extractInt(array $data, string $key): int
    {
        $value = $data[$key] ?? 0;

        return is_int($value) ? $value : (is_numeric($value) ? (int) $value : 0);
    }
}
