<?php

namespace App\SymfonyRepository;

use App\SymfonyEntity\BackgroundTask;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<\App\SymfonyEntity\BackgroundTask>
 */
class BackgroundTaskRepository extends ServiceEntityRepository {

  public function __construct(ManagerRegistry $registry) {
    parent::__construct($registry, BackgroundTask::class);
  }

  //    /**
  //     * @return BackgroundTask[] Returns an array of BackgroundTask objects
  //     */
  //    public function findByExampleField($value): array
  //    {
  //        return $this->createQueryBuilder('b')
  //            ->andWhere('b.exampleField = :val')
  //            ->setParameter('val', $value)
  //            ->orderBy('b.id', 'ASC')
  //            ->setMaxResults(10)
  //            ->getQuery()
  //            ->getResult()
  //        ;
  //    }

  //    public function findOneBySomeField($value): ?BackgroundTask
  //    {
  //        return $this->createQueryBuilder('b')
  //            ->andWhere('b.exampleField = :val')
  //            ->setParameter('val', $value)
  //            ->getQuery()
  //            ->getOneOrNullResult()
  //        ;
  //    }
}
