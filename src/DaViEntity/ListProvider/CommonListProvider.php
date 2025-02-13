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

  public function getEntityList(string | EntityInterface $entityClass, FilterContainer $filterContainer): EntityList {
    if($entityClass instanceof EntityInterface) {
      $entityClass = $entityClass::class;
    }

    $client = $filterContainer->getClient();

    $options = [EntityDataMapperInterface::OPTION_WITH_COLUMNS => false];
    $queryBuilder = $this->queryLocator->getBaseQuery($entityClass)->buildQueryFromSchema($entityClass, $client, $options);

    $schema = $this->entityTypeSchemaRegister->getSchemaFromEntityClass($entityClass);

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