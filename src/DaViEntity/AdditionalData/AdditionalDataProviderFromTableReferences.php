<?php

namespace App\DaViEntity\AdditionalData;

use App\Database\BaseQuery\BaseQueryLocator;
use App\Database\TableReference\TableReferenceHandlerLocator;
use App\Database\Traits\ExecuteQueryBuilderTrait;
use App\DaViEntity\DaViEntityManager;
use App\DaViEntity\EntityCreator\EntityCreatorLocator;
use App\DaViEntity\EntityDataMapperInterface;
use App\DaViEntity\EntityInterface;

class AdditionalDataProviderFromTableReferences implements AdditionalDataProviderInterface {

  use ExecuteQueryBuilderTrait;

  public function __construct(
    private readonly TableReferenceHandlerLocator $tableReferenceHandlerLocator,
    private readonly DaViEntityManager $entityManager,
    private readonly EntityCreatorLocator $entityCreatorLocator,
    private readonly BaseQueryLocator $queryLocator,
  ) {}

  public function loadData(EntityInterface $entity): void {
    $schema = $entity->getSchema();
    foreach ($schema->iterateTableReferences() as $internalName => $tableReference) {
      $handler = $this->tableReferenceHandlerLocator->getTableHandlerFromConfiguration($tableReference);
      $columns = $schema->getTableReferenceColumns($internalName);
      $options = [EntityDataMapperInterface::OPTION_COLUMNS => $columns];
      $queryBuilder = $this->queryLocator->getBaseQuery($entity)->buildQueryFromSchema($entity, $entity->getClient(), $options);
      $handler->joinTableConditionValue($queryBuilder, $tableReference, $entity);
      $result = $this->executeQueryBuilderInternal($queryBuilder, [], []);
      $result = array_reduce($result, 'array_merge_recursive', []);
      $entityCreator = $this->entityCreatorLocator->getEntityCreator($entity::class);
      $entityCreator->processRow($entity, $result);
    }
  }

}