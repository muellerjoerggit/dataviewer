<?php

namespace App\EntityServices\AdditionalData;

use App\Database\BaseQuery\BaseQueryLocator;
use App\Database\TableReference\TableReferenceHandlerLocator;
use App\Database\Traits\ExecuteQueryBuilderTrait;
use App\DaViEntity\DaViEntityManager;
use App\DaViEntity\EntityDataMapperInterface;
use App\DaViEntity\EntityInterface;
use App\EntityServices\Creator\CreatorLocator;

class AdditionalDataProviderFromTableReferences implements AdditionalDataProviderInterface {

  use ExecuteQueryBuilderTrait;

  public function __construct(
    private readonly TableReferenceHandlerLocator $tableReferenceHandlerLocator,
    private readonly DaViEntityManager $entityManager,
    private readonly CreatorLocator $entityCreatorLocator,
    private readonly BaseQueryLocator $queryLocator,
  ) {}

  public function loadData(EntityInterface $entity): void {
    $schema = $entity->getSchema();
    foreach ($schema->iterateTableReferences() as $internalName => $tableReference) {
      $handler = $this->tableReferenceHandlerLocator->getTableHandlerFromConfiguration($tableReference);
      $columns = $schema->getTableReferenceColumns($internalName);
      if(empty($columns)) {
        continue;
      }
      $options = [EntityDataMapperInterface::OPTION_COLUMNS => $columns];
      $queryBuilder = $handler->getReferencedTableQuery($tableReference, $entity, $options);
      $result = $this->executeQueryBuilderInternal($queryBuilder, [], []);
      $result = array_reduce($result, 'array_merge_recursive', []);
      $entityCreator = $this->entityCreatorLocator->getEntityCreator($entity::class, $entity->getClient());
      $entityCreator->processRow($entity, $result);
    }
  }

}