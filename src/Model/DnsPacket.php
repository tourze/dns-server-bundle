<?php

declare(strict_types=1);

namespace DnsServerBundle\Model;

use DnsServerBundle\Exception\InvalidArgumentDnsServerException;

class DnsPacket
{
    private string $id;

    private bool $isResponse;

    private int $opcode;

    private bool $authoritative;

    private bool $truncated;

    private bool $recursionDesired;

    private bool $recursionAvailable;

    private int $rcode;

    /** @var array<int, array{name: string, type: int, class: int}> */
    private array $questions = [];

    /** @var array<int, mixed> */
    private array $answers = [];

    /** @var array<int, mixed> */
    private array $authorities = [];

    /** @var array<int, mixed> */
    private array $additionals = [];

    public function __construct(string $data)
    {
        $header = unpack('nid/nflags/nqdcount/nancount/nnscount/narcount', $data);
        if (false === $header) {
            throw new InvalidArgumentDnsServerException('Invalid DNS packet header');
        }
        $headerId = $header['id'];
        assert(is_int($headerId));
        $this->id = pack('n', $headerId);
        $flags = $header['flags'];
        assert(is_int($flags));
        $this->isResponse = (bool) ($flags & 0x8000);
        $this->opcode = ($flags >> 11) & 0x000F;
        $this->authoritative = (bool) ($flags & 0x0400);
        $this->truncated = (bool) ($flags & 0x0200);
        $this->recursionDesired = (bool) ($flags & 0x0100);
        $this->recursionAvailable = (bool) ($flags & 0x0080);
        $this->rcode = $flags & 0x000F;

        $offset = 12;
        for ($i = 0; $i < $header['qdcount']; ++$i) {
            [$name, $len] = $this->readName($data, $offset);
            $offset += $len;
            $question = unpack('ntype/nclass', substr($data, $offset, 4));
            if (false === $question) {
                throw new InvalidArgumentDnsServerException('Invalid DNS question section');
            }
            $offset += 4;
            $qType = $question['type'];
            $qClass = $question['class'];
            assert(is_int($qType));
            assert(is_int($qClass));
            $this->questions[] = [
                'name' => $name,
                'type' => $qType,
                'class' => $qClass,
            ];
        }
    }

    /** @return array{0: string, 1: int} */
    private function readName(string $data, int $offset): array
    {
        $name = '';
        $len = 0;
        while (true) {
            $labelLen = ord($data[$offset]);
            if (0 === $labelLen) {
                ++$len;
                break;
            }
            if ($labelLen >= 0xC0) {
                $unpackResult = unpack('n', substr($data, $offset, 2));
                if (false === $unpackResult) {
                    throw new InvalidArgumentDnsServerException('Invalid DNS name pointer');
                }
                $pointerValue = $unpackResult[1];
                assert(is_int($pointerValue));
                $pointer = $pointerValue & 0x3FFF;
                [$suffix] = $this->readName($data, $pointer);
                $name .= ('' !== $name ? '.' : '') . $suffix;
                $len += 2;
                break;
            }
            $name .= ('' !== $name ? '.' : '') . substr($data, $offset + 1, $labelLen);
            $offset += $labelLen + 1;
            $len += $labelLen + 1;
        }

        return [$name, $len];
    }

    public function getId(): string
    {
        return $this->id;
    }

    /** @return array<int, array{name: string, type: int, class: int}> */
    public function getQuestions(): array
    {
        return $this->questions;
    }

    public function isResponse(): bool
    {
        return $this->isResponse;
    }

    public function getOpcode(): int
    {
        return $this->opcode;
    }

    public function isAuthoritative(): bool
    {
        return $this->authoritative;
    }

    public function isTruncated(): bool
    {
        return $this->truncated;
    }

    public function getRcode(): int
    {
        return $this->rcode;
    }

    /** @return array<int, mixed> */
    public function getAnswers(): array
    {
        return $this->answers;
    }

    /** @return array<int, mixed> */
    public function getAuthorities(): array
    {
        return $this->authorities;
    }

    /** @return array<int, mixed> */
    public function getAdditionals(): array
    {
        return $this->additionals;
    }

    /** @param array<int, array{name: string, type: int, class: int, ttl: int, data: string}> $answers */
    public function buildResponse(array $answers): string
    {
        $unpackResult = unpack('n', $this->id);
        if (false === $unpackResult) {
            throw new InvalidArgumentDnsServerException('Invalid DNS packet ID');
        }
        $response = pack('n', $unpackResult[1]);
        $flags = 0x8000;  // QR = 1, response
        if ($this->recursionDesired) {
            $flags |= 0x0100;  // RD
        }
        if ($this->recursionAvailable) {
            $flags |= 0x0080;  // RA
        }
        $response .= pack('n', $flags);
        $response .= pack('n', count($this->questions));  // QDCOUNT
        $response .= pack('n', count($answers));          // ANCOUNT
        $response .= pack('n', 0);                        // NSCOUNT
        $response .= pack('n', 0);                        // ARCOUNT

        // Questions
        foreach ($this->questions as $q) {
            $response .= $this->writeName($q['name']);
            $response .= pack('nn', $q['type'], $q['class']);
        }

        // Answers
        foreach ($answers as $a) {
            $response .= $this->writeName($a['name']);
            $response .= pack('nnNn', $a['type'], $a['class'], $a['ttl'], strlen($a['data']));
            $response .= $a['data'];
        }

        return $response;
    }

    private function writeName(string $name): string
    {
        $result = '';
        foreach (explode('.', $name) as $label) {
            $result .= chr(strlen($label)) . $label;
        }

        return $result . "\0";
    }
}
