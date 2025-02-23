<?php

namespace App\Development\Controller;

use App\Database\SqlFilter\FilterContainer;
use App\Database\SqlFilter\SqlFilterBuilder;
use App\DaViEntity\DaViEntityManager;
use App\DaViEntity\EntityKey;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use App\DaViEntity\ViewBuilder\ViewBuilderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Entities extends AbstractController {

  public function getEntities(DaViEntityManager $entityManager): void {
    $container = new FilterContainer('umbrella');
    dd($entityManager->loadMultipleEntities('RoleUserMap', $container));
  }

  public function getEntityByEntityKey(DaViEntityManager $entityManager): void {
    $entityKey = EntityKey::createFromString('umbrella::User::usr_id::2');
    dd($entityManager->loadEntityByEntityKey($entityKey));
  }

  public function preRenderEntity(DaViEntityManager $entityManager): void {
    $entityKey = EntityKey::createFromString('umbrella::User::usr_id::1');
    dd($entityManager->preRenderEntity($entityKey));
  }

  public function searchExtendedEntityOverview(
    DaViEntityManager $entityManager,
    EntityTypeSchemaRegister $schemaRegister,
    SqlFilterBuilder $sqlFilterBuilder
  ): void {
    //    $entityType = 'User';
    $entityType = 'Role';
    $filters = [];
    $client = 'umbrella';
    $schema = $schemaRegister->getEntityTypeSchema($entityType);
    $filterContainer = $sqlFilterBuilder->buildFilterContainerFromArray($client, $schema, $filters);
    if (!$schema->isSingleColumnPrimaryKeyInteger()) {
      $filterContainer->setLimit(500);
    }

    $entityList = $entityManager->getEntityListFromEntityType($entityType, $filterContainer);
    $entities = [];

    foreach ($entityList->iterateEntityList() as $entityKeyString => $entityData) {
      $entityKey = EntityKey::createFromString($entityKeyString);
      $overView['entityOverview'] = $entityManager->getExtendedEntityOverview($entityKey, [ViewBuilderInterface::FORMAT => FALSE]);
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