<?php

namespace DnsServerBundle\Model;

final class DNSRecord extends EntityAbstract implements DNSRecordInterface
{
    /**
     * @var string
     */
    private const DATA = 'data';

    public function __construct(
        private DNSRecordType $recordType,
        private Hostname $hostname,
        private int $TTL,
        private ?IPAddress $IPAddress = null,
        private string $class = 'IN',
        private ?DataAbstract $data = null,
    ) {
    }

    public static function createFromPrimitives(
        string $recordType,
        string $hostname,
        int $ttl,
        ?string $IPAddress = null,
        string $class = 'IN',
        ?string $data = null,
    ): DNSRecord {
        $type = DNSRecordType::createFromString($recordType);
        $hostname = Hostname::createFromString($hostname);
        $data = (null !== $data)
            ? DataAbstract::createFromTypeAndString($type, $data)
            : null;

        return new self(
            $type,
            $hostname,
            $ttl,
            null !== $IPAddress ? IPAddress::createFromString($IPAddress) : null,
            $class,
            $data
        );
    }

    public function getType(): DNSRecordType
    {
        return $this->recordType;
    }

    public function getHostname(): Hostname
    {
        return $this->hostname;
    }

    public function getTTL(): int
    {
        return $this->TTL;
    }

    public function getIPAddress(): ?IPAddress
    {
        return $this->IPAddress;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getData(): ?DataAbstract
    {
        return $this->data;
    }

    public function setData(DataAbstract $data): void
    {
        $this->data = $data;
    }

    public function setTTL(int $ttl): void
    {
        $this->TTL = $ttl;
    }

    public function toArray(): array
    {
        $formatted = [
            'hostname' => (string) $this->hostname,
            'type' => (string) $this->recordType,
            'TTL' => $this->TTL,
            'class' => $this->class,
        ];

        if (null !== $this->IPAddress) {
            $formatted['IPAddress'] = (string) $this->IPAddress;
        }

        if (null !== $this->data) {
            $formatted[self::DATA] = (string) $this->data;
        }

        return $formatted;
    }

    public function equals(DNSRecordInterface $record): bool
    {
        return $this->hostname->equals($record->getHostname())
            && $this->recordType->equals($record->getType())
            && (string) $this->data === (string) $record->getData() // could be null
            && (string) $this->IPAddress === (string) $record->getIPAddress(); // could be null
    }

    public function __serialize(): array
    {
        return $this->toArray();
    }

    /** @param array<string, mixed> $unserialized */
    public function __unserialize(array $unserialized): void
    {
        $rawIPAddres = $unserialized['IPAddress'] ?? null;
        $rawType = $unserialized['type'] ?? '';
        $rawHostname = $unserialized['hostname'] ?? '';
        $rawClass = $unserialized['class'] ?? 'IN';
        $rawData = $unserialized[self::DATA] ?? null;
        $rawTTL = $unserialized['TTL'] ?? 0;

        $this->recordType = DNSRecordType::createFromString(is_string($rawType) ? $rawType : '');
        $this->hostname = Hostname::createFromString(is_string($rawHostname) ? $rawHostname : '');
        $this->TTL = is_int($rawTTL) ? $rawTTL : (is_numeric($rawTTL) ? (int) $rawTTL : 0);
        $this->IPAddress = (\is_string($rawIPAddres) && '' !== $rawIPAddres) ? IPAddress::createFromString($rawIPAddres) : null;
        $this->class = is_string($rawClass) ? $rawClass : 'IN';
        $this->data = is_string($rawData)
            ? DataAbstract::createFromTypeAndString($this->recordType, $rawData)
            : null;
    }

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
