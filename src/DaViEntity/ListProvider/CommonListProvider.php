<?php

namespace App\DaViEntity\ListProvider;

use App\Database\BaseQuery\BaseQueryLocator;
use App\Database\DatabaseLocator;
use App\Database\SqlFilter\FilterContainer;
use App\Database\SqlFilter\SqlFilterBuilder;
use App\Database\Traits\ExecuteQueryBuilderTrait;
use App\DataCollections\EntityList;
use App\DaViEntity\ColumnBuilder\ColumnBuilderLocator;
use App\DaViEntity\EntityDataMapperInterface;
use App\DaViEntity\EntityInterface;
use App\DaViEntity\EntityLabel\LabelCrafterLocator;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;

class CommonListProvider implements ListProviderInterface {

  use ExecuteQueryBuilderTrait;

  public function __construct(
    private readonly EntityTypeSchemaRegister $entityTypeSchemaRegister,
    private readonly SqlFilterBuilder $sqlFilterBuilder,
    private readonly DatabaseLocator $databaseLocator,
    private readonly BaseQueryLocator $queryLocator,
    private readonly ColumnBuilderLocator $sqlEntityLabelCrafterLocator,
    private readonly LabelCrafterLocator $entityLabelCrafterLocator,
    private readonly ColumnBuilderLocator $entityColumnBuilderLocator,
  ) {}

  public function getEntityList(string $entityClass, FilterContainer $filterContainer): EntityList {
    $client = $filterContainer->getClient();

    $options = [EntityDataMapperInterface::OPTION_WITH_COLUMNS => false];
    $schema = $this->entityTypeSchemaRegister->getSchemaFromEntityClass($entityClass);
    $queryBuilder = $this->queryLocator->getBaseQuery($schema, $client)->buildQueryFromSchema($entityClass, $client, $options);

    $entityColumnBuilder = $this->entityColumnBuilderLocator->getEntityColumnBuilder($entityClass, $filterContainer->getClient());

    $entityColumnBuilder->buildLabelColumn($queryBuilder, $entityClass);
    $entityColumnBuilder->buildEntityKeyColumn($queryBuilder, $entityClass);

    $queryBuilder->setMaxResults($filterContainer->getLimit());

    $this->sqlFilterBuilder->buildFilteredQueryMultipleFilters($queryBuilder, $filterContainer, $schema);

    $countQueryBuilder = clone $queryBuilder;
    $queryResult = $this->executeQueryBuilderInternal($queryBuilder, [], []);
    $database = $this->databaseLocator->getDatabaseBySchema($schema);
    $entityCount = $database->getCountResultFromQueryBuilder($countQueryBuilder);

    if(!is_integer($entityCount)) {
      $entityCount = -1;
    }

    $list = new EntityList();
    $list
      ->setUseBound($schema->isSingleColumnPrimaryKeyInteger())
      ->setTotalCount($entityCount)
      ->addEntities($queryResult)
    ;

    return $list;
  }

}