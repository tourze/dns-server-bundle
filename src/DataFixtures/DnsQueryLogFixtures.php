<?php

declare(strict_types=1);

namespace DnsServerBundle\DataFixtures;

use DnsServerBundle\Entity\DnsQueryLog;
use DnsServerBundle\Enum\RecordType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class DnsQueryLogFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $dnsQueryLog1 = new DnsQueryLog();
        $dnsQueryLog1->setDomain('test-domain.local');
        $dnsQueryLog1->setQueryType(RecordType::A);
        $dnsQueryLog1->setClientIp('192.168.1.1');
        $dnsQueryLog1->setResponse('192.0.2.1');
        $dnsQueryLog1->setIsHit(false);
        $dnsQueryLog1->setResponseTime(50);

        $dnsQueryLog2 = new DnsQueryLog();
        $dnsQueryLog2->setDomain('google.com');
        $dnsQueryLog2->setQueryType(RecordType::AAAA);
        $dnsQueryLog2->setClientIp('192.168.1.2');
        $dnsQueryLog2->setResponse('2001:db8::1');
        $dnsQueryLog2->setIsHit(true);
        $dnsQueryLog2->setResponseTime(20);

        $dnsQueryLog3 = new DnsQueryLog();
        $dnsQueryLog3->setDomain('cloudflare.com');
        $dnsQueryLog3->setQueryType(RecordType::MX);
        $dnsQueryLog3->setClientIp('10.0.0.1');
        $dnsQueryLog3->setResponse('mail.cloudflare.com');
        $dnsQueryLog3->setIsHit(false);
        $dnsQueryLog3->setResponseTime(75);

        $manager->persist($dnsQueryLog1);
        $manager->persist($dnsQueryLog2);
        $manager->persist($dnsQueryLog3);

        $manager->flush();
    }
}
