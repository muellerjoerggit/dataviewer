<?php

namespace App\SymfonyRepository;

use App\SymfonyEntity\Version;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Version>
 */
class VersionRepository extends ServiceEntityRepository {

  public function __construct(ManagerRegistry $registry) {
    parent::__construct($registry, Version::class);
  }

  //    /**
  //     * @return Version[] Returns an array of Version objects
  //     */
  //    public function findByExampleField($value): array
  //    {
  //        return $this->createQueryBuilder('v')
  //            ->andWhere('v.exampleField = :val')
  //            ->setParameter('val', $value)
  //            ->orderBy('v.id', 'ASC')
  //            ->setMaxResults(10)
  //            ->getQuery()
  //            ->getResult()
  //        ;
  //    }

  //    public function findOneBySomeField($value): ?Version
  //    {
  //        return $this->createQueryBuilder('v')
  //            ->andWhere('v.exampleField = :val')
  //            ->setParameter('val', $value)
  //            ->getQuery()
  //            ->getOneOrNullResult()
  //        ;
  //    }
}
