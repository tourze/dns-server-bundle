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
use Tourze\DoctrineIpBundle\Traits\IpTraceableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

/**
 * @implements PlainArrayInterface<string, mixed>
 * @implements ApiArrayInterface<string, mixed>
 * @implements AdminArrayInterface<string, mixed>
 */
#[ORM\Entity(repositoryClass: UpstreamDnsServerRepository::class)]
#[ORM\Table(name: 'dns_upstream_server', options: ['comment' => '上游DNS服务器'])]
class UpstreamDnsServer implements PlainArrayInterface, ApiArrayInterface, AdminArrayInterface, \Stringable
{
    use BlameableAware;
    use IpTraceableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = null;

    #[ORM\Column(length: 64, options: ['comment' => '服务器名称'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 64)]
    #[IndexColumn]
    private string $name = '';

    #[ORM\Column(length: 255, options: ['comment' => '服务器地址'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $host = '';

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
    #[Assert\Length(max: 65535)]
    private ?string $description = null;

    #[ORM\Column(length: 255, options: ['comment' => '域名匹配模式'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $pattern = '';

    #[ORM\Column(type: Types::STRING, enumType: MatchStrategy::class, options: ['comment' => '匹配策略'])]
    #[Assert\NotNull]
    #[Assert\Choice(callback: [MatchStrategy::class, 'cases'])]
    private MatchStrategy $strategy = MatchStrategy::EXACT;

    #[ORM\Column(options: ['comment' => '是否为默认服务器'])]
    #[Assert\NotNull]
    #[Assert\Type(type: 'boolean')]
    private bool $isDefault = false;

    /**
     * @var array<int, string>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '自定义应答IP列表'])]
    #[Assert\Type(type: 'array')]
    private ?array $customAnswers = null;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'TTL(秒)'])]
    #[Assert\NotNull]
    #[Assert\Type(type: 'integer')]
    #[Assert\PositiveOrZero]
    private int $ttl = 300;

    #[ORM\Column(type: Types::STRING, enumType: DnsProtocolEnum::class, options: ['comment' => 'DNS协议类型'])]
    #[Assert\NotNull]
    #[Assert\Choice(callback: [DnsProtocolEnum::class, 'cases'])]
    private DnsProtocolEnum $protocol = DnsProtocolEnum::UDP;

    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '证书路径'])]
    #[Assert\Length(max: 255)]
    private ?string $certPath = null;

    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '私钥路径'])]
    #[Assert\Length(max: 255)]
    private ?string $keyPath = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否验证证书', 'default' => true])]
    #[Assert\NotNull]
    #[Assert\Type(type: 'boolean')]
    private bool $verifyCert = true;

    #[IndexColumn]
    #[TrackColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    #[Assert\Type(type: 'boolean')]
    private ?bool $valid = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function setHost(string $host): void
    {
        $this->host = $host;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function setPort(int $port): void
    {
        $this->port = $port;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }

    public function setTimeout(int $timeout): void
    {
        $this->timeout = $timeout;
    }

    public function getWeight(): int
    {
        return $this->weight;
    }

    public function setWeight(int $weight): void
    {
        $this->weight = $weight;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getPattern(): string
    {
        return $this->pattern;
    }

    public function setPattern(string $pattern): void
    {
        $this->pattern = $pattern;
    }

    public function getStrategy(): MatchStrategy
    {
        return $this->strategy;
    }

    public function setStrategy(MatchStrategy $strategy): void
    {
        $this->strategy = $strategy;
    }

    public function isDefault(): bool
    {
        return $this->isDefault;
    }

    public function getIsDefault(): bool
    {
        return $this->isDefault;
    }

    public function setIsDefault(bool $isDefault): void
    {
        $this->isDefault = $isDefault;
    }

    /**
     * @return array<int, string>|null
     */
    public function getCustomAnswers(): ?array
    {
        return $this->customAnswers;
    }

    /**
     * @param array<int, string>|null $customAnswers
     */
    public function setCustomAnswers(?array $customAnswers): void
    {
        $this->customAnswers = $customAnswers;
    }

    public function getTtl(): int
    {
        return $this->ttl;
    }

    public function setTtl(int $ttl): void
    {
        $this->ttl = $ttl;
    }

    public function getProtocol(): DnsProtocolEnum
    {
        return $this->protocol;
    }

    public function setProtocol(DnsProtocolEnum $protocol): void
    {
        $this->protocol = $protocol;
    }

    public function getCertPath(): ?string
    {
        return $this->certPath;
    }

    public function setCertPath(?string $certPath): void
    {
        $this->certPath = $certPath;
    }

    public function getKeyPath(): ?string
    {
        return $this->keyPath;
    }

    public function setKeyPath(?string $keyPath): void
    {
        $this->keyPath = $keyPath;
    }

    public function isVerifyCert(): bool
    {
        return $this->verifyCert;
    }

    public function setVerifyCert(bool $verifyCert): void
    {
        $this->verifyCert = $verifyCert;
    }

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(?bool $valid): void
    {
        $this->valid = $valid;
    }

    /**
     * 返回时间戳相关的数组
     */
    /**
     * @return array<string, mixed>
     */
    private function retrieveTimestampArray(): array
    {
        return [
            'createdFromIp' => $this->getCreatedFromIp(),
            'updatedFromIp' => $this->getUpdatedFromIp(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function retrievePlainArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->name ?? null,
            'host' => $this->host ?? null,
            'port' => $this->port ?? null,
            'timeout' => $this->timeout ?? null,
            'weight' => $this->weight ?? null,
            'description' => $this->description,
            'pattern' => $this->pattern ?? null,
            'strategy' => $this->strategy->value,
            'isDefault' => $this->isDefault ?? false,
            'customAnswers' => $this->customAnswers,
            'ttl' => $this->ttl ?? null,
            'protocol' => $this->protocol->value,
            'certPath' => $this->certPath,
            'keyPath' => $this->keyPath,
            'verifyCert' => $this->verifyCert ?? true,
            'valid' => $this->isValid(),
            'createdBy' => $this->getCreatedBy(),
            'updatedBy' => $this->getUpdatedBy(),
            ...$this->retrieveTimestampArray(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function retrieveApiArray(): array
    {
        return $this->retrievePlainArray();
    }

    /**
     * @return array<string, mixed>
     */
    public function retrieveAdminArray(): array
    {
        return $this->retrieveApiArray();
    }

    public function __toString(): string
    {
        return sprintf('%s (%s:%d)', $this->name, $this->host, $this->port);
    }
}
