<?php

declare(strict_types=1);

namespace DnsServerBundle\Repository;

use DnsServerBundle\Entity\DnsQueryLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DnsQueryLog>
 *
 * @method DnsQueryLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method DnsQueryLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method DnsQueryLog[] findAll()
 * @method DnsQueryLog[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DnsQueryLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DnsQueryLog::class);
    }

    /**
     * @return DnsQueryLog[]
     */
    public function findByDomain(string $domain, int $limit = 10): array
    {
        return $this->createQueryBuilder('d')
            ->where('d.domain = :domain')
            ->setParameter('domain', $domain)
            ->orderBy('d.id', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return DnsQueryLog[]
     */
    public function findByClientIp(string $clientIp, int $limit = 10): array
    {
        return $this->createQueryBuilder('d')
            ->where('d.clientIp = :clientIp')
            ->setParameter('clientIp', $clientIp)
            ->orderBy('d.id', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
