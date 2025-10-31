<?php

declare(strict_types=1);

namespace DnsServerBundle\DataFixtures;

use DnsServerBundle\Entity\UpstreamDnsServer;
use DnsServerBundle\Enum\DnsProtocolEnum;
use DnsServerBundle\Enum\MatchStrategy;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class UpstreamDnsServerFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $dnsServer1 = new UpstreamDnsServer();
        $dnsServer1->setName('Cloudflare DNS');
        $dnsServer1->setHost('1.1.1.1');
        $dnsServer1->setPort(53);
        $dnsServer1->setTimeout(5);
        $dnsServer1->setWeight(10);
        $dnsServer1->setDescription('Cloudflare 公共 DNS');
        $dnsServer1->setPattern('*');
        $dnsServer1->setStrategy(MatchStrategy::WILDCARD);
        $dnsServer1->setIsDefault(true);
        $dnsServer1->setTtl(300);
        $dnsServer1->setProtocol(DnsProtocolEnum::UDP);
        $dnsServer1->setVerifyCert(true);
        $dnsServer1->setValid(true);

        $dnsServer2 = new UpstreamDnsServer();
        $dnsServer2->setName('Google DNS');
        $dnsServer2->setHost('8.8.8.8');
        $dnsServer2->setPort(53);
        $dnsServer2->setTimeout(5);
        $dnsServer2->setWeight(8);
        $dnsServer2->setDescription('Google 公共 DNS');
        $dnsServer2->setPattern('*.google.com');
        $dnsServer2->setStrategy(MatchStrategy::WILDCARD);
        $dnsServer2->setIsDefault(false);
        $dnsServer2->setTtl(300);
        $dnsServer2->setProtocol(DnsProtocolEnum::UDP);
        $dnsServer2->setVerifyCert(true);
        $dnsServer2->setValid(true);

        $dnsServer3 = new UpstreamDnsServer();
        $dnsServer3->setName('Local DNS over TLS');
        $dnsServer3->setHost('dns.local');
        $dnsServer3->setPort(853);
        $dnsServer3->setTimeout(10);
        $dnsServer3->setWeight(5);
        $dnsServer3->setDescription('本地 DNS over TLS 服务器');
        $dnsServer3->setPattern('*.local');
        $dnsServer3->setStrategy(MatchStrategy::SUFFIX);
        $dnsServer3->setIsDefault(false);
        $dnsServer3->setTtl(60);
        $dnsServer3->setProtocol(DnsProtocolEnum::DOT);
        $dnsServer3->setCertPath('/etc/ssl/certs/dns.local.crt');
        $dnsServer3->setKeyPath('/etc/ssl/private/dns.local.key');
        $dnsServer3->setVerifyCert(true);
        $dnsServer3->setValid(false);

        $manager->persist($dnsServer1);
        $manager->persist($dnsServer2);
        $manager->persist($dnsServer3);

        $manager->flush();
    }
}
