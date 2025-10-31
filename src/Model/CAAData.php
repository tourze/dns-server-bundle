<?php

namespace DnsServerBundle\Model;

use DnsServerBundle\Exception\InvalidArgumentDnsServerException;

final class CAAData extends DataAbstract implements \Stringable
{
    private ?string $value;

    public function __construct(private int $flags, private string $tag, ?string $value = null)
    {
        $this->value = null !== $value
            ? $this->normalizeValue($value)
            : null;
    }

    public function __toString(): string
    {
        return "{$this->flags} {$this->tag} \"{$this->value}\"";
    }

    /** @param array<string, mixed> $unserialized */
    public function __unserialize(array $unserialized): void
    {
        $rawFlags = $unserialized['flags'] ?? 0;
        $rawTag = $unserialized['tag'] ?? '';

        $this->flags = is_int($rawFlags) ? $rawFlags : (is_numeric($rawFlags) ? (int) $rawFlags : 0);
        $this->tag = is_string($rawTag) ? $rawTag : '';
        $this->value = isset($unserialized['value']) && is_string($unserialized['value'])
            ? $unserialized['value']
            : null;
    }

    public function getFlags(): int
    {
        return $this->flags;
    }

    public function getTag(): string
    {
        return $this->tag;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function toArray(): array
    {
        return [
            'flags' => $this->flags,
            'tag' => $this->tag,
            'value' => $this->value,
        ];
    }

    private function normalizeValue(string $value): string
    {
        $normalized = \trim(\str_ireplace('"', '', $value));

        if (1 === \preg_match('/\s/m', $normalized)) {
            throw new InvalidArgumentDnsServerException("{$value} is not a valid CAA value");
        }

        return $normalized;
    }
}
