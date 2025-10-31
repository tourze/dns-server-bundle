<?php

namespace DnsServerBundle\Model;

use DnsServerBundle\Exception\InvalidArgumentDnsServerException;
use Tourze\Arrayable\Arrayable;

/**
 * @implements \ArrayAccess<int, DNSRecordInterface>
 * @implements \Iterator<int, DNSRecordInterface|null>
 * @implements Arrayable<int, DNSRecordInterface>
 */
final class DNSRecordCollection extends EntityAbstract implements \ArrayAccess, \Iterator, \Countable, Arrayable, Serializable
{
    /** @var \ArrayIterator<int, DNSRecordInterface> */
    private \ArrayIterator $records;

    public function __construct(DNSRecordInterface ...$records)
    {
        $this->records = new \ArrayIterator(\array_values($records));
    }

    public function toArray(): array
    {
        return $this->records->getArrayCopy();
    }

    public function pickFirst(): ?DNSRecordInterface
    {
        $copy = $this->records->getArrayCopy();

        return \array_shift($copy);
    }

    public function filteredByType(DNSRecordType $type): self
    {
        $fn = fn (DNSRecordInterface $record) => $record->getType()->equals($type);

        return new self(...\array_filter($this->records->getArrayCopy(), $fn));
    }

    public function has(DNSRecordInterface $lookupRecord): bool
    {
        foreach ($this->records->getArrayCopy() as $record) {
            if ($lookupRecord->equals($record)) {
                return true;
            }
        }

        return false;
    }

    public function current(): ?DNSRecordInterface
    {
        if (!$this->valid()) {
            return null;
        }

        $current = $this->records->current();
        assert($current instanceof DNSRecordInterface);

        return $current;
    }

    public function next(): void
    {
        $this->records->next();
    }

    public function key(): int
    {
        $key = $this->records->key();

        return is_int($key) ? $key : 0;
    }

    public function valid(): bool
    {
        return $this->records->valid();
    }

    public function rewind(): void
    {
        $this->records->rewind();
    }

    public function offsetExists($offset): bool
    {
        return $this->records->offsetExists($offset);
    }

    public function offsetGet($offset): DNSRecordInterface
    {
        $record = $this->records->offsetGet($offset);
        assert($record instanceof DNSRecordInterface);

        return $record;
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     * @throws InvalidArgumentDnsServerException
     */
    public function offsetSet($offset, $value): void
    {
        if (!$value instanceof DNSRecordInterface) {
            throw new InvalidArgumentDnsServerException('Invalid value');
        }

        // Ensure offset is int|null for ArrayIterator
        $safeOffset = is_int($offset) || is_null($offset) ? $offset : null;
        $this->records->offsetSet($safeOffset, $value);
    }

    public function offsetUnset($offset): void
    {
        $this->records->offsetUnset($offset);
    }

    public function count(): int
    {
        return $this->records->count();
    }

    public function isEmpty(): bool
    {
        return 0 === $this->count();
    }

    public function __serialize(): array
    {
        return ['records' => $this->records->getArrayCopy()];
    }

    /** @param array<string, mixed> $data */
    public function __unserialize(array $data): void
    {
        if (isset($data['records']) && is_array($data['records'])) {
            // Validate and filter to ensure all elements are DNSRecordInterface
            $filtered = array_filter(
                $data['records'],
                fn ($item) => $item instanceof DNSRecordInterface
            );
            /** @var array<int, DNSRecordInterface> $reindexed */
            $reindexed = array_values($filtered);
            $this->records = new \ArrayIterator($reindexed);
        } else {
            // Backward compatibility: assume direct array of records
            $filtered = array_filter(
                $data,
                fn ($item) => $item instanceof DNSRecordInterface
            );
            /** @var array<int, DNSRecordInterface> $reindexed */
            $reindexed = array_values($filtered);
            $this->records = new \ArrayIterator($reindexed);
        }
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function jsonSerialize(): array
    {
        // Convert array<int, DNSRecordInterface> to array<string, array<string, mixed>> by serializing each record
        $result = [];
        $index = 0;
        foreach ($this->toArray() as $record) {
            $result['record_' . $index++] = $record->jsonSerialize();
        }

        return $result;
    }

    public function withUniqueValuesExcluded(): self
    {
        return $this->filterValues(
            fn (DNSRecordInterface $candidateRecord, DNSRecordCollection $remaining): bool => $remaining->has(
                $candidateRecord
            )
        )->withUniqueValues();
    }

    public function withUniqueValues(): self
    {
        return $this->filterValues(
            fn (DNSRecordInterface $candidateRecord, DNSRecordCollection $remaining): bool => !$remaining->has(
                $candidateRecord
            )
        );
    }

    private function filterValues(callable $eval): self
    {
        $filtered = new self();
        $records = $this->records->getArrayCopy();

        while (($record = \array_shift($records)) !== null) {
            if ($eval($record, new self(...$records))) {
                $filtered[] = $record;
            }
        }

        return $filtered;
    }
}
