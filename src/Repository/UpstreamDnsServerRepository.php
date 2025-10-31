<?php

declare(strict_types=1);

namespace DnsServerBundle\Repository;

use DnsServerBundle\Entity\UpstreamDnsServer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<UpstreamDnsServer>
 */
#[AsRepository(entityClass: UpstreamDnsServer::class)]
class UpstreamDnsServerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UpstreamDnsServer::class);
    }

    /**
     * @return array<UpstreamDnsServer>
     */
    public function findAllValid(): array
    {
        /** @var array<UpstreamDnsServer> */
        return $this->createQueryBuilder('u')
            ->where('u.valid = :valid')
            ->setParameter('valid', true)
            ->orderBy('u.weight', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getDefaultServer(): ?UpstreamDnsServer
    {
        return $this->findOneBy(['isDefault' => true, 'valid' => true]);
    }

    public function save(UpstreamDnsServer $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UpstreamDnsServer $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
