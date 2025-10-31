<?php

declare(strict_types=1);

namespace DnsServerBundle\Service;

use DnsServerBundle\Entity\UpstreamDnsServer;
use DnsServerBundle\Repository\UpstreamDnsServerRepository;

class UpstreamServerMatcherService
{
    public function __construct(
        private readonly UpstreamDnsServerRepository $repository,
        private readonly DnsMatcherService $dnsMatcherService,
    ) {
    }

    public function findMatchingServer(string $domain): ?UpstreamDnsServer
    {
        $servers = $this->repository->findBy(['valid' => true], ['id' => 'ASC']);

        foreach ($servers as $server) {
            if ($this->dnsMatcherService->isMatch($domain, $server->getPattern(), $server->getStrategy())) {
                return $server;
            }
        }

        return null;
    }

    public function getDefaultServer(): ?UpstreamDnsServer
    {
        return $this->repository->getDefaultServer();
    }

    public function findMatchingOrDefaultServer(string $domain): ?UpstreamDnsServer
    {
        return $this->findMatchingServer($domain) ?? $this->getDefaultServer();
    }
}
