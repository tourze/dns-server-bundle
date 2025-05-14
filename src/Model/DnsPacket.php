<?php

declare(strict_types=1);

namespace DnsServerBundle\Model;

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
    private array $questions = [];
    private array $answers = [];
    private array $authorities = [];
    private array $additionals = [];

    public function __construct(string $data)
    {
        $header = unpack('nid/nflags/nqdcount/nancount/nnscount/narcount', $data);
        $this->id = pack('n', $header['id']);
        $flags = $header['flags'];
        $this->isResponse = (bool)($flags & 0x8000);
        $this->opcode = ($flags >> 11) & 0x000F;
        $this->authoritative = (bool)($flags & 0x0400);
        $this->truncated = (bool)($flags & 0x0200);
        $this->recursionDesired = (bool)($flags & 0x0100);
        $this->recursionAvailable = (bool)($flags & 0x0080);
        $this->rcode = $flags & 0x000F;

        $offset = 12;
        for ($i = 0; $i < $header['qdcount']; $i++) {
            [$name, $len] = $this->readName($data, $offset);
            $offset += $len;
            $question = unpack('ntype/nclass', substr($data, $offset, 4));
            $offset += 4;
            $this->questions[] = [
                'name' => $name,
                'type' => $question['type'],
                'class' => $question['class'],
            ];
        }
    }

    private function readName(string $data, int $offset): array
    {
        $name = '';
        $len = 0;
        while (true) {
            $labelLen = ord($data[$offset]);
            if ($labelLen === 0) {
                $len++;
                break;
            }
            if ($labelLen >= 0xC0) {
                $pointer = unpack('n', substr($data, $offset, 2))[1] & 0x3FFF;
                [$suffix] = $this->readName($data, $pointer);
                $name .= ($name ? '.' : '') . $suffix;
                $len += 2;
                break;
            }
            $name .= ($name ? '.' : '') . substr($data, $offset + 1, $labelLen);
            $offset += $labelLen + 1;
            $len += $labelLen + 1;
        }
        return [$name, $len];
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getQuestions(): array
    {
        return $this->questions;
    }

    public function buildResponse(array $answers): string
    {
        $response = pack('n', unpack('n', $this->id)[1]);
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
