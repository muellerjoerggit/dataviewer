<?php

namespace App\DaViEntity\DataProvider;

use App\Database\BaseQuery\BaseQueryLocator;
use App\Database\SqlFilter\FilterContainer;
use App\Database\SqlFilter\SqlFilterBuilder;
use App\Database\Traits\ExecuteQueryBuilderTrait;
use App\DaViEntity\EntityInterface;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;

class CommonSqlEntityDataProvider implements EntityDataProviderInterface {

  use ExecuteQueryBuilderTrait;

  public function __construct(
    protected readonly SqlFilterBuilder $sqlFilterBuilder,
    protected readonly BaseQueryLocator $queryLocator,
    protected readonly EntityTypeSchemaRegister $entityTypeSchemaRegister,
  ) {}

  public function fetchEntityData(string | EntityInterface $entityClass, FilterContainer $filters, array $options = []): array {
    $client = $filters->getClient();
    $schema = $this->entityTypeSchemaRegister->getSchemaFromEntityClass($entityClass);
    $queryBuilder = $this->queryLocator->getBaseQuery($entityClass)->buildQueryFromSchema($entityClass, $client, $options);

    $this->sqlFilterBuilder->buildFilteredQueryMultipleFilters($queryBuilder, $filters, $schema);
    $queryBuilder->setMaxResults($filters->getLimit());

    return $this->executeQueryBuilderInternal($queryBuilder, [], []);
  }

}