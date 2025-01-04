<?php

namespace App\Development\Controller;

use App\Database\DaViDatabaseOne;
use App\Database\SqlFilter\FilterContainer;
use App\DaViEntity\DaViEntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Database extends AbstractController {

  public function connection(DaViDatabaseOne $database): void {
    dd($database->getConnection('employees'));
  }

  public function testDb(DaViDatabaseOne $database): void {
    $queryBuilder = $database->createQueryBuilder('employees');
    $queryBuilder
      ->select('*')
      ->from('employees')
      ->setMaxResults(100);
    dd($queryBuilder->fetchAllAssociative());
  }

  public function fetchData(DaViEntityManager $entityManager): void {
    $container = new FilterContainer('employees');
    dd($entityManager->loadEntityData('Employee', $container));
  }

  public function fetchEntityList(DaViEntityManager $entityManager): void {
    $container = new FilterContainer('employees');
    dd($entityManager->getEntityList('Employee', $container));
  }

}