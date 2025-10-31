<?php

namespace DnsServerBundle\Model;

use DnsServerBundle\Exception\InvalidArgumentDnsServerException;

final class IPAddress extends EntityAbstract implements \Stringable
{
    private string $IPAddress;

    /**
     * @throws InvalidArgumentDnsServerException
     */
    public function __construct(string $IPAddress)
    {
        $IPAddress = \trim($IPAddress);

        if (!self::isValid($IPAddress)) {
            throw new InvalidArgumentDnsServerException("{$IPAddress} is not a valid IP address");
        }

        $this->IPAddress = $IPAddress;
    }

    public function __toString(): string
    {
        return $this->IPAddress;
    }

    public static function isValid(string $IPAddress): bool
    {
        return (bool) \filter_var($IPAddress, FILTER_VALIDATE_IP);
    }

    public static function createFromString(string $IPAddress): IPAddress
    {
        return new self($IPAddress);
    }

    public function equals(IPAddress $IPAddress): bool
    {
        return $this->IPAddress === (string) $IPAddress;
    }

    public function getIPAddress(): string
    {
        return $this->IPAddress;
    }

    public function isIPv6(): bool
    {
        return (bool) \filter_var($this->IPAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
    }

    public function isIPv4(): bool
    {
        return (bool) \filter_var($this->IPAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
    }
}
