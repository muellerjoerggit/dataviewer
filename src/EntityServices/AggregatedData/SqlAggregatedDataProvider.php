<?php

namespace App\EntityServices\AggregatedData;

use App\Database\Aggregation\AggregationHandlerLocator;
use App\Database\AggregationHandler\Attribute\AggregationDefinitionInterface;
use App\Database\BaseQuery\BaseQueryLocator;
use App\Database\SqlFilter\FilterContainer;
use App\Database\SqlFilter\SqlFilterBuilder;
use App\DataCollections\TableData;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;

class SqlAggregatedDataProvider implements AggregatedDataProviderInterface {

  public function __construct(
    private readonly SqlFilterBuilder $sqlFilterBuilder,
    private readonly BaseQueryLocator $queryLocator,
    private readonly EntityTypeSchemaRegister $entityTypeSchemaRegister,
    private readonly AggregationHandlerLocator $aggregationHandlerLocator,
  ) {}

  public function fetchAggregatedData(string $entityClass, string $client, AggregationDefinitionInterface $aggregationDefinition, FilterContainer $filterContainer = null, array $options = []): TableData {
    $schema = $this->entityTypeSchemaRegister->getSchemaFromEntityClass($entityClass);
    $queryBuilder = $this->queryLocator->getBaseQuery($schema, $client)->buildQueryFromSchema($schema->getEntityClass(), $client, $options);
    $queryBuilder->setMaxResults(null);

    $this->sqlFilterBuilder->buildFilteredQueryMultipleFilters($queryBuilder, $filterContainer, $schema);

    $handler = $this->aggregationHandlerLocator->getAggregationHandler($aggregationDefinition);
    $handler->buildAggregatedQueryBuilder($schema, $queryBuilder, $aggregationDefinition, $options);

    return $handler->processingAggregatedData($queryBuilder, $schema, $aggregationDefinition);
  }

}