<?php

declare(strict_types=1);

namespace DnsServerBundle\Entity;

use DnsServerBundle\Enum\RecordType;
use DnsServerBundle\Repository\DnsQueryLogRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineIpBundle\Traits\IpTraceableAware;
use Tourze\DoctrineTimestampBundle\Traits\CreateTimeAware;

/**
 * @implements PlainArrayInterface<string, mixed>
 * @implements AdminArrayInterface<string, mixed>
 */
#[ORM\Entity(repositoryClass: DnsQueryLogRepository::class)]
#[ORM\Table(name: 'dns_query_log', options: ['comment' => 'DNS查询日志'])]
#[ORM\Index(name: 'dns_query_log_domain_query_type_idx', columns: ['domain', 'query_type'])]
class DnsQueryLog implements PlainArrayInterface, AdminArrayInterface, \Stringable
{
    use CreateTimeAware;
    use IpTraceableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '查询域名'])]
    #[IndexColumn]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $domain = '';

    #[ORM\Column(type: Types::INTEGER, enumType: RecordType::class, options: ['comment' => '查询类型(A/AAAA/MX等)'])]
    #[IndexColumn]
    #[Assert\NotNull]
    #[Assert\Choice(callback: [RecordType::class, 'cases'])]
    private RecordType $queryType = RecordType::A;

    #[ORM\Column(type: Types::STRING, length: 39, options: ['comment' => '客户端IP'])]
    #[IndexColumn]
    #[Assert\NotBlank]
    #[Assert\Length(max: 39)]
    #[Assert\Ip]
    private string $clientIp = '';

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => 'DNS响应内容'])]
    #[Assert\Length(max: 65535)]
    private ?string $response = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否命中缓存'])]
    #[IndexColumn]
    #[Assert\NotNull]
    #[Assert\Type(type: 'boolean')]
    private bool $isHit = false;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '响应时间(毫秒)'])]
    #[Assert\NotNull]
    #[Assert\Type(type: 'integer')]
    #[Assert\PositiveOrZero]
    private int $responseTime = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function setDomain(string $domain): void
    {
        $this->domain = $domain;
    }

    public function getQueryType(): RecordType
    {
        return $this->queryType;
    }

    public function setQueryType(RecordType $queryType): void
    {
        $this->queryType = $queryType;
    }

    public function getClientIp(): string
    {
        return $this->clientIp;
    }

    public function setClientIp(string $clientIp): void
    {
        $this->clientIp = $clientIp;
    }

    public function getResponse(): ?string
    {
        return $this->response;
    }

    public function setResponse(?string $response): void
    {
        $this->response = $response;
    }

    public function isHit(): bool
    {
        return $this->isHit;
    }

    public function getIsHit(): bool
    {
        return $this->isHit;
    }

    public function setIsHit(bool $isHit): void
    {
        $this->isHit = $isHit;
    }

    public function getResponseTime(): int
    {
        return $this->responseTime;
    }

    public function setResponseTime(int $responseTime): void
    {
        $this->responseTime = $responseTime;
    }

    /**
     * @return array<string, mixed>
     */
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

    /**
     * @return array<string, mixed>
     */
    public function retrievePlainArray(): array
    {
        return $this->toPlainArray();
    }

    /**
     * @return array<string, mixed>
     */
    public function retrieveAdminArray(): array
    {
        return $this->toPlainArray();
    }

    public function __toString(): string
    {
        return sprintf('%s (%s) - %s', $this->domain, $this->queryType->value, $this->clientIp);
    }
}
