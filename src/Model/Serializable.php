<?php

namespace DnsServerBundle\Model;

interface Serializable extends \JsonSerializable
{
    public function __serialize(): array;

    /** @param array<string, mixed> $data */
    public function __unserialize(array $data): void;

    /** @return array<string, mixed> */
    public function jsonSerialize(): array;
}
