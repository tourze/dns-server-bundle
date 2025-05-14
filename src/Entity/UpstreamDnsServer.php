<?php

declare(strict_types=1);

namespace DnsServerBundle\Entity;

use DnsServerBundle\Enum\DnsProtocolEnum;
use DnsServerBundle\Enum\MatchStrategy;
use DnsServerBundle\Repository\UpstreamDnsServerRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\ApiArrayInterface;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineIpBundle\Attribute\CreateIpColumn;
use Tourze\DoctrineIpBundle\Attribute\UpdateIpColumn;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;

#[ORM\Entity(repositoryClass: UpstreamDnsServerRepository::class)]
#[ORM\Table(name: 'dns_upstream_server', options: ['comment' => '上游DNS服务器'])]
class UpstreamDnsServer implements PlainArrayInterface, ApiArrayInterface, AdminArrayInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[ORM\Column(length: 64, options: ['comment' => '服务器名称'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 64)]
    #[IndexColumn]
    private string $name;

    #[ORM\Column(length: 255, options: ['comment' => '服务器地址'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $host;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '端口号'])]
    #[Assert\NotBlank]
    #[Assert\Range(min: 1, max: 65535)]
    private int $port = 53;

    #[ORM\Column(type: Types::SMALLINT, options: ['comment' => '超时时间(秒)'])]
    #[Assert\NotBlank]
    #[Assert\Range(min: 1, max: 10000)]
    private int $timeout = 5;

    #[ORM\Column(type: Types::SMALLINT, options: ['comment' => '权重'])]
    #[Assert\NotBlank]
    #[Assert\Range(min: 0, max: 100)]
    private int $weight = 1;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '描述'])]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private string $pattern;

    #[ORM\Column(type: Types::STRING, enumType: MatchStrategy::class)]
    private MatchStrategy $strategy;

    #[ORM\Column]
    private bool $isDefault = false;

    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '自定义应答IP列表'])]
    private ?array $customAnswers = null;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'TTL(秒)'])]
    private int $ttl = 300;

    #[ORM\Column(type: Types::STRING, enumType: DnsProtocolEnum::class)]
    private DnsProtocolEnum $protocol = DnsProtocolEnum::UDP;

    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '证书路径'])]
    private ?string $certPath = null;

    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '私钥路径'])]
    private ?string $keyPath = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否验证证书', 'default' => true])]
    private bool $verifyCert = true;

    #[IndexColumn]
    #[TrackColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    private ?bool $valid = false;

    #[CreatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '创建人'])]
    private ?string $createdBy = null;

    #[UpdatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '更新人'])]
    private ?string $updatedBy = null;

    #[CreateIpColumn]
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '创建时IP'])]
    private ?string $createdFromIp = null;

    #[UpdateIpColumn]
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '更新时IP'])]
    private ?string $updatedFromIp = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function setHost(string $host): self
    {
        $this->host = $host;
        return $this;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function setPort(int $port): self
    {
        $this->port = $port;
        return $this;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }

    public function setTimeout(int $timeout): self
    {
        $this->timeout = $timeout;
        return $this;
    }

    public function getWeight(): int
    {
        return $this->weight;
    }

    public function setWeight(int $weight): self
    {
        $this->weight = $weight;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getPattern(): string
    {
        return $this->pattern;
    }

    public function setPattern(string $pattern): self
    {
        $this->pattern = $pattern;
        return $this;
    }

    public function getStrategy(): MatchStrategy
    {
        return $this->strategy;
    }

    public function setStrategy(MatchStrategy $strategy): self
    {
        $this->strategy = $strategy;
        return $this;
    }

    public function isDefault(): bool
    {
        return $this->isDefault;
    }

    public function setIsDefault(bool $isDefault): self
    {
        $this->isDefault = $isDefault;
        return $this;
    }

    public function getCustomAnswers(): ?array
    {
        return $this->customAnswers;
    }

    public function setCustomAnswers(?array $customAnswers): self
    {
        $this->customAnswers = $customAnswers;
        return $this;
    }

    public function getTtl(): int
    {
        return $this->ttl;
    }

    public function setTtl(int $ttl): self
    {
        $this->ttl = $ttl;
        return $this;
    }

    public function getProtocol(): DnsProtocolEnum
    {
        return $this->protocol;
    }

    public function setProtocol(DnsProtocolEnum $protocol): self
    {
        $this->protocol = $protocol;
        return $this;
    }

    public function getCertPath(): ?string
    {
        return $this->certPath;
    }

    public function setCertPath(?string $certPath): self
    {
        $this->certPath = $certPath;
        return $this;
    }

    public function getKeyPath(): ?string
    {
        return $this->keyPath;
    }

    public function setKeyPath(?string $keyPath): self
    {
        $this->keyPath = $keyPath;
        return $this;
    }

    public function isVerifyCert(): bool
    {
        return $this->verifyCert;
    }

    public function setVerifyCert(bool $verifyCert): self
    {
        $this->verifyCert = $verifyCert;
        return $this;
    }

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(?bool $valid): self
    {
        $this->valid = $valid;

        return $this;
    }

    public function setCreatedBy(?string $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }

    public function setUpdatedBy(?string $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    public function getUpdatedBy(): ?string
    {
        return $this->updatedBy;
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

    public function retrievePlainArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->name,
            'host' => $this->host,
            'port' => $this->port,
            'timeout' => $this->timeout,
            'weight' => $this->weight,
            'description' => $this->description,
            'pattern' => $this->pattern,
            'strategy' => $this->strategy->value,
            'isDefault' => $this->isDefault,
            'customAnswers' => $this->customAnswers,
            'ttl' => $this->ttl,
            'protocol' => $this->protocol->value,
            'certPath' => $this->certPath,
            'keyPath' => $this->keyPath,
            'verifyCert' => $this->verifyCert,
            'valid' => $this->isValid(),
            'createdBy' => $this->getCreatedBy(),
            'updatedBy' => $this->getUpdatedBy(),
            ...$this->retrieveTimestampArray(),
        ];
    }

    public function retrieveApiArray(): array
    {
        return $this->retrievePlainArray();
    }

    public function retrieveAdminArray(): array
    {
        return $this->retrieveApiArray();
    }
}
