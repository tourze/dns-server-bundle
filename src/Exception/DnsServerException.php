<?php

namespace DnsServerBundle\Exception;

abstract class DnsServerException extends \Exception implements \JsonSerializable
{
    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        return [
            'message' => $this->getMessage(),
            'code' => $this->getCode(),
            'file' => $this->getFile(),
            'line' => $this->getLine(),
            'trace' => $this->getTraceAsString(),
        ];
    }
}
