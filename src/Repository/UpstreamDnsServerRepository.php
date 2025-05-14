<?php

declare(strict_types=1);

namespace DnsServerBundle\Repository;

use DnsServerBundle\Entity\UpstreamDnsServer;
use DnsServerBundle\Service\DnsMatcherService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UpstreamDnsServer>
 *
 * @method UpstreamDnsServer|null find($id, $lockMode = null, $lockVersion = null)
 * @method UpstreamDnsServer|null findOneBy(array $criteria, array $orderBy = null)
 * @method UpstreamDnsServer[] findAll()
 * @method UpstreamDnsServer[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UpstreamDnsServerRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly DnsMatcherService $dnsMatcherService,
    )
    {
        parent::__construct($registry, UpstreamDnsServer::class);
    }

    /**
     * @return UpstreamDnsServer[]
     */
    public function findAllValid(): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.valid = :valid')
            ->setParameter('valid', true)
            ->orderBy('u.weight', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findMatchingServer(string $domain): ?UpstreamDnsServer
    {
        $servers = $this->findBy(['enabled' => true], ['id' => 'ASC']);
        
        foreach ($servers as $server) {
            if ($this->dnsMatcherService->isMatch($domain, $server->getPattern(), $server->getStrategy())) {
                return $server;
            }
        }
        
        return null;
    }

    public function getDefaultServer(): ?UpstreamDnsServer
    {
        return $this->findOneBy(['isDefault' => true, 'enabled' => true]);
    }
}
