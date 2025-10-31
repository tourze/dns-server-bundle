<?php

namespace DnsServerBundle\Tests\Entity;

use DnsServerBundle\Entity\UpstreamDnsServer;
use DnsServerBundle\Enum\DnsProtocolEnum;
use DnsServerBundle\Enum\MatchStrategy;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(UpstreamDnsServer::class)]
final class UpstreamDnsServerTest extends AbstractEntityTestCase
{
    /**
     * @return array<string, array{string, mixed}>
     */
    public static function propertiesProvider(): array
    {
        return [
            'name' => ['name', 'Test Server'],
            'host' => ['host', '8.8.8.8'],
            'port' => ['port', 5353],
            'timeout' => ['timeout', 10],
            'weight' => ['weight', 5],
            'description' => ['description', 'Test description'],
            'pattern' => ['pattern', '*.example.com'],
            'strategy' => ['strategy', MatchStrategy::SUFFIX],
            'isDefault' => ['isDefault', true],
            'isDefaultFalse' => ['isDefault', false],
            'customAnswers' => ['customAnswers', ['192.168.1.1', '192.168.1.2']],
            'ttl' => ['ttl', 600],
            'protocol' => ['protocol', DnsProtocolEnum::DOH],
            'certPath' => ['certPath', '/path/to/cert.pem'],
            'keyPath' => ['keyPath', '/path/to/key.pem'],
            'verifyCert' => ['verifyCert', true],
            'verifyCertFalse' => ['verifyCert', false],
            'valid' => ['valid', true],
            'validFalse' => ['valid', false],
            'createdBy' => ['createdBy', 'admin'],
            'updatedBy' => ['updatedBy', 'admin'],
            'createdFromIp' => ['createdFromIp', '127.0.0.1'],
            'updatedFromIp' => ['updatedFromIp', '127.0.0.1'],
        ];
    }

    protected function createEntity(): object
    {
        $entity = new UpstreamDnsServer();
        $entity->setName('Test Server');
        $entity->setHost('8.8.8.8');
        $entity->setPort(5353);
        $entity->setTimeout(10);
        $entity->setWeight(5);
        $entity->setDescription('Test description');
        $entity->setPattern('*.example.com');
        $entity->setStrategy(MatchStrategy::SUFFIX);
        $entity->setIsDefault(true);
        $entity->setCustomAnswers(['192.168.1.1', '192.168.1.2']);
        $entity->setTtl(600);
        $entity->setProtocol(DnsProtocolEnum::DOH);
        $entity->setCertPath('/path/to/cert.pem');
        $entity->setKeyPath('/path/to/key.pem');
        $entity->setVerifyCert(true);
        $entity->setValid(true);
        $entity->setCreatedBy('admin');
        $entity->setUpdatedBy('admin');
        $entity->setCreatedFromIp('127.0.0.1');
        $entity->setUpdatedFromIp('127.0.0.1');

        return $entity;
    }

    public function testDefaultValues(): void
    {
        $server = new UpstreamDnsServer();
        $this->assertNull($server->getId());
        $this->assertSame(53, $server->getPort());
        $this->assertSame(5, $server->getTimeout());
        $this->assertSame(1, $server->getWeight());
        $this->assertSame(300, $server->getTtl());
        $this->assertSame(DnsProtocolEnum::UDP, $server->getProtocol());
        $this->assertTrue($server->isVerifyCert());
        $this->assertFalse($server->isDefault());
        $this->assertFalse($server->isValid());
    }

    public function testSetGetName(): void
    {
        $name = 'Test Server';
        $server = new UpstreamDnsServer();
        $server->setName($name);
        $this->assertSame($name, $server->getName());
    }

    public function testSetGetHost(): void
    {
        $host = '8.8.8.8';
        $server = new UpstreamDnsServer();
        $server->setHost($host);
        $this->assertSame($host, $server->getHost());
    }

    public function testSetGetPort(): void
    {
        $port = 5353;
        $server = new UpstreamDnsServer();
        $server->setPort($port);
        $this->assertSame($port, $server->getPort());
    }

    public function testSetGetTimeout(): void
    {
        $timeout = 10;
        $server = new UpstreamDnsServer();
        $server->setTimeout($timeout);
        $this->assertSame($timeout, $server->getTimeout());
    }

    public function testSetGetWeight(): void
    {
        $weight = 5;
        $server = new UpstreamDnsServer();
        $server->setWeight($weight);
        $this->assertSame($weight, $server->getWeight());
    }

    public function testSetGetDescription(): void
    {
        $description = 'Test description';
        $server = new UpstreamDnsServer();
        $server->setDescription($description);
        $this->assertSame($description, $server->getDescription());
    }

    public function testSetGetPattern(): void
    {
        $pattern = '*.example.com';
        $server = new UpstreamDnsServer();
        $server->setPattern($pattern);
        $this->assertSame($pattern, $server->getPattern());
    }

    public function testSetGetStrategy(): void
    {
        $strategy = MatchStrategy::SUFFIX;
        $server = new UpstreamDnsServer();
        $server->setStrategy($strategy);
        $this->assertSame($strategy, $server->getStrategy());
    }

    public function testSetIsDefault(): void
    {
        $server = new UpstreamDnsServer();
        $server->setIsDefault(true);
        $this->assertTrue($server->isDefault());

        $server->setIsDefault(false);
        $this->assertFalse($server->isDefault());
    }

    public function testSetGetCustomAnswers(): void
    {
        $answers = ['192.168.1.1', '192.168.1.2'];
        $server = new UpstreamDnsServer();
        $server->setCustomAnswers($answers);
        $this->assertSame($answers, $server->getCustomAnswers());
    }

    public function testSetGetTtl(): void
    {
        $ttl = 600;
        $server = new UpstreamDnsServer();
        $server->setTtl($ttl);
        $this->assertSame($ttl, $server->getTtl());
    }

    public function testSetGetProtocol(): void
    {
        $protocol = DnsProtocolEnum::DOH;
        $server = new UpstreamDnsServer();
        $server->setProtocol($protocol);
        $this->assertSame($protocol, $server->getProtocol());
    }

    public function testSetGetCertPath(): void
    {
        $certPath = '/path/to/cert.pem';
        $server = new UpstreamDnsServer();
        $server->setCertPath($certPath);
        $this->assertSame($certPath, $server->getCertPath());
    }

    public function testSetGetKeyPath(): void
    {
        $keyPath = '/path/to/key.pem';
        $server = new UpstreamDnsServer();
        $server->setKeyPath($keyPath);
        $this->assertSame($keyPath, $server->getKeyPath());
    }

    public function testSetVerifyCert(): void
    {
        $server = new UpstreamDnsServer();
        $server->setVerifyCert(false);
        $this->assertFalse($server->isVerifyCert());

        $server->setVerifyCert(true);
        $this->assertTrue($server->isVerifyCert());
    }

    public function testSetValid(): void
    {
        $server = new UpstreamDnsServer();
        $server->setValid(true);
        $this->assertTrue($server->isValid());

        $server->setValid(false);
        $this->assertFalse($server->isValid());
    }

    public function testRetrievePlainArray(): void
    {
        $server = new UpstreamDnsServer();
        $server->setName('Test Server');
        $server->setHost('8.8.8.8');
        $server->setPort(53);
        $server->setPattern('*.example.com');
        $server->setStrategy(MatchStrategy::SUFFIX);

        $array = $server->retrievePlainArray();
        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('host', $array);
        $this->assertArrayHasKey('port', $array);
        $this->assertArrayHasKey('pattern', $array);
        $this->assertArrayHasKey('strategy', $array);

        $this->assertSame('Test Server', $array['name']);
        $this->assertSame('8.8.8.8', $array['host']);
        $this->assertSame(53, $array['port']);
        $this->assertSame('*.example.com', $array['pattern']);
        $this->assertSame('suffix', $array['strategy']);
    }

    public function testRetrieveApiArray(): void
    {
        $server = new UpstreamDnsServer();
        $array = $server->retrieveApiArray();
        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('name', $array);
    }

    public function testRetrieveAdminArray(): void
    {
        $server = new UpstreamDnsServer();
        $array = $server->retrieveAdminArray();
        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('name', $array);
    }

    public function testSetGetCreatedBy(): void
    {
        $createdBy = 'admin';
        $server = new UpstreamDnsServer();
        $server->setCreatedBy($createdBy);
        $this->assertSame($createdBy, $server->getCreatedBy());
    }

    public function testSetGetUpdatedBy(): void
    {
        $updatedBy = 'admin';
        $server = new UpstreamDnsServer();
        $server->setUpdatedBy($updatedBy);
        $this->assertSame($updatedBy, $server->getUpdatedBy());
    }

    public function testSetGetCreatedFromIp(): void
    {
        $ip = '127.0.0.1';
        $server = new UpstreamDnsServer();
        $server->setCreatedFromIp($ip);
        $this->assertSame($ip, $server->getCreatedFromIp());
    }

    public function testSetGetUpdatedFromIp(): void
    {
        $ip = '127.0.0.1';
        $server = new UpstreamDnsServer();
        $server->setUpdatedFromIp($ip);
        $this->assertSame($ip, $server->getUpdatedFromIp());
    }
}
