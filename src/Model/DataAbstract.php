<?php

namespace DnsServerBundle\Model;

use DnsServerBundle\Exception\InvalidArgumentDnsServerException;
use Tourze\Arrayable\Arrayable;

/**
 * @implements Arrayable<string, mixed>
 */
abstract class DataAbstract implements Arrayable, Serializable, \Stringable
{
    abstract public function __toString(): string;

    abstract public function toArray(): array;

    public function __serialize(): array
    {
        return $this->toArray();
    }

    public function equals(DataAbstract $dataAbstract): bool
    {
        return (string) $this === (string) $dataAbstract;
    }

    /**
     * @throws InvalidArgumentDnsServerException
     */
    public static function createFromTypeAndString(DNSRecordType $recordType, string $data): self
    {
        return match (true) {
            $recordType->isA(DNSRecordType::TYPE_TXT) => self::createTxtData($data),
            $recordType->isA(DNSRecordType::TYPE_NS) => self::createNsData($data),
            $recordType->isA(DNSRecordType::TYPE_CNAME) => self::createCnameData($data),
            $recordType->isA(DNSRecordType::TYPE_PTR) => self::createPtrData($data),
            default => self::createComplexRecordData($recordType, $data),
        };
    }

    private static function createTxtData(string $data): TXTData
    {
        return new TXTData(\trim($data, '"'));
    }

    private static function createNsData(string $data): NSData
    {
        return new NSData(new Hostname($data));
    }

    private static function createCnameData(string $data): CNAMEData
    {
        return new CNAMEData(new Hostname($data));
    }

    private static function createPtrData(string $data): PTRData
    {
        return new PTRData(new Hostname($data));
    }

    /**
     * @throws InvalidArgumentDnsServerException
     */
    private static function createComplexRecordData(DNSRecordType $recordType, string $data): self
    {
        $parsed = self::parseDataToArray($data);

        return match (true) {
            $recordType->isA(DNSRecordType::TYPE_MX) => self::createMxData($parsed),
            $recordType->isA(DNSRecordType::TYPE_SOA) => self::createSoaData($parsed),
            $recordType->isA(DNSRecordType::TYPE_CAA) => self::createCaaData($parsed),
            $recordType->isA(DNSRecordType::TYPE_SRV) => self::createSrvData($parsed),
            default => throw new InvalidArgumentDnsServerException("{$data} could not be created with type {$recordType}"),
        };
    }

    /** @param array<int, string> $parsed */
    private static function createMxData(array $parsed): MXData
    {
        return new MXData(new Hostname($parsed[1]), (int) $parsed[0]);
    }

    /** @param array<int, string> $parsed */
    private static function createSoaData(array $parsed): SOAData
    {
        return new SOAData(
            new Hostname($parsed[0]),
            new Hostname($parsed[1]),
            (int) ($parsed[2] ?? 0),
            (int) ($parsed[3] ?? 0),
            (int) ($parsed[4] ?? 0),
            (int) ($parsed[5] ?? 0),
            (int) ($parsed[6] ?? 0)
        );
    }

    /**
     * @throws InvalidArgumentDnsServerException
     */
    /** @param array<int, string> $parsed */
    private static function createCaaData(array $parsed): CAAData
    {
        if (3 !== \count($parsed)) {
            throw new InvalidArgumentDnsServerException('CAA record requires exactly 3 parts');
        }

        return new CAAData((int) $parsed[0], (string) $parsed[1], $parsed[2]);
    }

    /** @param array<int, string> $parsed */
    private static function createSrvData(array $parsed): SRVData
    {
        return new SRVData(
            0 !== (int) $parsed[0] ? (int) $parsed[0] : 0,
            0 !== (int) $parsed[1] ? (int) $parsed[1] : 0,
            0 !== (int) $parsed[2] ? (int) $parsed[2] : 0,
            new Hostname($parsed[3])
        );
    }

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    protected function init(): void
    {
    }

    /** @return array<int, string> */
    private static function parseDataToArray(string $data): array
    {
        return \explode(' ', $data);
    }
}
