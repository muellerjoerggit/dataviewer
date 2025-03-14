<?php

namespace App\SymfonyRepository;

use App\SymfonyEntity\File;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<\App\SymfonyEntity\File>
 */
class FileRepository extends ServiceEntityRepository {

  public function __construct(ManagerRegistry $registry) {
    parent::__construct($registry, File::class);
  }

  //    /**
  //     * @return File[] Returns an array of File objects
  //     */
  //    public function findByExampleField($value): array
  //    {
  //        return $this->createQueryBuilder('f')
  //            ->andWhere('f.exampleField = :val')
  //            ->setParameter('val', $value)
  //            ->orderBy('f.id', 'ASC')
  //            ->setMaxResults(10)
  //            ->getQuery()
  //            ->getResult()
  //        ;
  //    }

  //    public function findOneBySomeField($value): ?File
  //    {
  //        return $this->createQueryBuilder('f')
  //            ->andWhere('f.exampleField = :val')
  //            ->setParameter('val', $value)
  //            ->getQuery()
  //            ->getOneOrNullResult()
  //        ;
  //    }
}
