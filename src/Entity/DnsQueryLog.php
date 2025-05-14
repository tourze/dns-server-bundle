<?php

declare(strict_types=1);

namespace DnsServerBundle\Entity;

use DnsServerBundle\Enum\RecordType;
use DnsServerBundle\Repository\DnsQueryLogRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineIpBundle\Attribute\CreateIpColumn;
use Tourze\DoctrineIpBundle\Attribute\UpdateIpColumn;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;

#[ORM\Entity(repositoryClass: DnsQueryLogRepository::class)]
#[ORM\Table(name: 'dns_query_log', options: ['comment' => 'DNS查询日志'])]
#[ORM\Index(name: 'dns_query_log_domain_query_type_idx', columns: ['domain', 'query_type'])]
class DnsQueryLog implements PlainArrayInterface, AdminArrayInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '查询域名'])]
    #[IndexColumn]
    private string $domain;

    #[ORM\Column(type: Types::STRING, length: 16, enumType: RecordType::class, options: ['comment' => '查询类型(A/AAAA/MX等)'])]
    #[IndexColumn]
    private RecordType $queryType;

    #[ORM\Column(type: Types::STRING, length: 39, options: ['comment' => '客户端IP'])]
    #[IndexColumn]
    private string $clientIp;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => 'DNS响应内容'])]
    private ?string $response = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否命中缓存'])]
    #[IndexColumn]
    private bool $isHit = false;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '响应时间(毫秒)'])]
    private int $responseTime = 0;

    #[CreateIpColumn]
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '创建时IP'])]
    private ?string $createdFromIp = null;

    #[UpdateIpColumn]
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '更新时IP'])]
    private ?string $updatedFromIp = null;

    #[IndexColumn]
    #[CreateTimeColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '创建时间'])]
    private ?\DateTimeInterface $createTime = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function setDomain(string $domain): self
    {
        $this->domain = $domain;
        return $this;
    }

    public function getQueryType(): RecordType
    {
        return $this->queryType;
    }

    public function setQueryType(RecordType $queryType): self
    {
        $this->queryType = $queryType;
        return $this;
    }

    public function getClientIp(): string
    {
        return $this->clientIp;
    }

    public function setClientIp(string $clientIp): self
    {
        $this->clientIp = $clientIp;
        return $this;
    }

    public function getResponse(): ?string
    {
        return $this->response;
    }

    public function setResponse(?string $response): self
    {
        $this->response = $response;
        return $this;
    }

    public function isHit(): bool
    {
        return $this->isHit;
    }

    public function setIsHit(bool $isHit): self
    {
        $this->isHit = $isHit;
        return $this;
    }

    public function getResponseTime(): int
    {
        return $this->responseTime;
    }

    public function setResponseTime(int $responseTime): self
    {
        $this->responseTime = $responseTime;
        return $this;
    }

    public function setCreatedFromIp(?string $createdFromIp): self
    {
        $this->createdFromIp = $createdFromIp;

        return $this;
    }

    public function getCreatedFromIp(): ?string
    {
        return $this->createdFromIp;
    }

    public function setUpdatedFromIp(?string $updatedFromIp): self
    {
        $this->updatedFromIp = $updatedFromIp;

        return $this;
    }

    public function getUpdatedFromIp(): ?string
    {
        return $this->updatedFromIp;
    }

    public function setCreateTime(?\DateTimeInterface $createdAt): self
    {
        $this->createTime = $createdAt;

        return $this;
    }

    public function getCreateTime(): ?\DateTimeInterface
    {
        return $this->createTime;
    }

    public function toPlainArray(): array
    {
        return [
            'id' => $this->getId(),
            'domain' => $this->domain,
            'queryType' => $this->queryType->value,
            'clientIp' => $this->clientIp,
            'response' => $this->response,
            'isHit' => $this->isHit,
            'responseTime' => $this->responseTime,
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
        ];
    }

    public function retrievePlainArray(): array
    {
        return $this->toPlainArray();
    }

    public function retrieveAdminArray(): array
    {
        return $this->toPlainArray();
    }
}
