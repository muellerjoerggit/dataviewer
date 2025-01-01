<?php

namespace App\Development\Controller;

use App\Database\SqlFilter\FilterContainer;
use App\Database\SqlFilter\SqlFilterBuilder;
use App\DaViEntity\DaViEntityManager;
use App\DaViEntity\EntityKey;
use App\DaViEntity\EntityViewBuilderInterface;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Entities extends AbstractController {

  public function getEntities(DaViEntityManager $entityManager): void {
    $container = new FilterContainer('employees');
    dd($entityManager->loadMultipleEntities('Employee', $container)[0]);
  }

  public function getEntityByEntityKey(DaViEntityManager $entityManager): void {
    $entityKey = EntityKey::createFromString('umbrella::Role::rol_id::2');
    dd($entityManager->loadEntityByEntityKey($entityKey));
  }

  public function preRenderEntity(DaViEntityManager $entityManager): void {
    $entityKey = EntityKey::createFromString('umbrella::RoleUserMap::usr_id+rol_id::11+1');
    dd($entityManager->preRenderEntity($entityKey));
  }

  public function searchExtendedEntityOverview(
    DaViEntityManager $entityManager,
    EntityTypeSchemaRegister $schemaRegister,
    SqlFilterBuilder $sqlFilterBuilder
  ): void {
    //    $entityType = 'User';
    $entityType = 'RoleUserMap';
    $filters = [];
    $client = 'umbrella';
    $schema = $schemaRegister->getEntityTypeSchema($entityType);
    $filterContainer = $sqlFilterBuilder->buildFilterContainerFromArray($client, $schema, $filters);
    if (!$schema->isSingleColumnPrimaryKeyInteger()) {
      $filterContainer->setLimit(500);
    }

    $entityList = $entityManager->getEntityList($entityType, $filterContainer);
    $entities = [];

    foreach ($entityList->iterateEntityList() as $entityKeyString => $entityData) {
      $entityKey = EntityKey::createFromString($entityKeyString);
      $overView['entityOverview'] = $entityManager->getExtendedEntityOverview($entityKey, [EntityViewBuilderInterface::FORMAT => FALSE]);
      $overView['entityKey'] = $entityKeyString;
      $overView['entityLabel'] = $entityData['entityLabel'];
      $entities[] = $overView;
    }

    $listArray = [
      'entities' => $entities,
      'entityCount' => $entityList->getTotalCount(),
      'lowerBound' => -1,
      'upperBound' => -1,
      'page' => $entityList->getPage(),
    ];

    if ($entityList->isUseBound()) {
      $listArray['lowerBound'] = $entityList->getLowerBound();
      $listArray['upperBound'] = $entityList->getUpperBound();
    }

    dd($listArray);
  }

}