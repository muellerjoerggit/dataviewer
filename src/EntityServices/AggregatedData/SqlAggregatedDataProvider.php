<?php

namespace App\EntityServices\AggregatedData;

use App\Database\Aggregation\AggregationBuilder;
use App\Database\Aggregation\AggregationConfiguration;
use App\Database\BaseQuery\BaseQueryLocator;
use App\Database\SqlFilter\FilterContainer;
use App\Database\SqlFilter\SqlFilterBuilder;
use App\DataCollections\TableData;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;

class SqlAggregatedDataProvider implements AggregatedDataProviderInterface {

  public function __construct(
    private readonly SqlFilterBuilder $sqlFilterBuilder,
    private readonly AggregationBuilder $aggregationBuilder,
    private readonly BaseQueryLocator $queryLocator,
    private readonly EntityTypeSchemaRegister $entityTypeSchemaRegister,
  ) {}

  public function fetchAggregatedData(string $entityClass, string $client, AggregationConfiguration $aggregationConfiguration, FilterContainer $filterContainer = null, array $options = []): TableData {
    $schema = $this->entityTypeSchemaRegister->getSchemaFromEntityClass($entityClass);
    $queryBuilder = $this->queryLocator->getBaseQuery($schema, $client)->buildQueryFromSchema($schema->getEntityClass(), $client, $options);

    $this->sqlFilterBuilder->buildFilteredQueryMultipleFilters($queryBuilder, $filterContainer, $schema);
    return $this->aggregationBuilder->fetchAggregatedData($schema, $queryBuilder, $aggregationConfiguration, $options);
  }

}