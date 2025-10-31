<?php

declare(strict_types=1);

namespace DnsServerBundle\Repository;

use DnsServerBundle\Entity\DnsQueryLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<DnsQueryLog>
 */
#[AsRepository(entityClass: DnsQueryLog::class)]
class DnsQueryLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DnsQueryLog::class);
    }

    /**
     * @return array<DnsQueryLog>
     */
    public function findByDomain(string $domain, int $limit = 10): array
    {
        /** @var array<DnsQueryLog> */
        return $this->createQueryBuilder('d')
            ->where('d.domain = :domain')
            ->setParameter('domain', $domain)
            ->orderBy('d.id', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return array<DnsQueryLog>
     */
    public function findByClientIp(string $clientIp, int $limit = 10): array
    {
        /** @var array<DnsQueryLog> */
        return $this->createQueryBuilder('d')
            ->where('d.clientIp = :clientIp')
            ->setParameter('clientIp', $clientIp)
            ->orderBy('d.id', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    public function save(DnsQueryLog $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DnsQueryLog $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
