<?php

namespace App\DaViEntity;

use App\Database\Aggregation\AggregationBuilder;
use App\Database\Aggregation\AggregationConfiguration;
use App\Database\DatabaseLocator;
use App\Database\DaViQueryBuilder;
use App\Database\SqlFilter\FilterContainer;
use App\Database\SqlFilter\SqlFilterBuilder;
use App\Database\Traits\ExecuteQueryBuilderTrait;
use App\DataCollections\EntityList;
use App\DaViEntity\Schema\EntitySchema;

class CommonEntityDataMapper implements EntityDataMapperInterface {

  use ExecuteQueryBuilderTrait;

  public function __construct(
    private readonly DatabaseLocator $databaseLocator,
    private readonly SqlFilterBuilder $sqlFilterBuilder,
    private readonly AggregationBuilder $aggregationBuilder,
  ) {}

  protected function getQueryBuilder(EntitySchema $schema, string $client): DaViQueryBuilder {
    return $this->databaseLocator->getDatabaseBySchema($schema)->createQueryBuilder($client);
  }

  public function buildQueryFromSchema(EntitySchema $schema, string $client, array $options = []): DaViQueryBuilder {
    $options = $this->getDefaultQueryOptions($options);

    $baseTable = $schema->getBaseTable();
    if (
      $options[EntityDataMapperInterface::OPTION_WITH_COLUMNS]
      && empty($options[EntityDataMapperInterface::OPTION_COLUMNS])
    ) {
      $columns = $schema->getColumns();
    } elseif (
      $options[EntityDataMapperInterface::OPTION_WITH_COLUMNS]
      && !empty($options[EntityDataMapperInterface::OPTION_COLUMNS])
    ) {
      $columns = $options[EntityDataMapperInterface::OPTION_COLUMNS];
    } else {
      $columns = [];
    }

    $queryBuilder = $this->getQueryBuilder($schema, $client);

    foreach ($columns as $property => $column) {
      $queryBuilder->addSelect($column . ' AS ' . $property);
    }

    $queryBuilder->from($baseTable);
    $queryBuilder->setMaxResults($options[EntityDataMapperInterface::OPTION_LIMIT]);

    return $queryBuilder;
  }

  protected function getDefaultQueryOptions(array $options): array {
    return array_merge(
      [
        EntityDataMapperInterface::OPTION_WITH_COLUMNS => true,
        EntityDataMapperInterface::OPTION_WITH_JOINS => true,
        EntityDataMapperInterface::OPTION_COLUMNS => [],
        EntityDataMapperInterface::OPTION_LIMIT => 50
      ],
      $options
    );
  }

  protected function executeQueryBuilder(DaViQueryBuilder $queryBuilder, array $options = []): mixed {
    return $this->executeQueryBuilderInternal($queryBuilder, $options, []);
  }

  public function fetchAggregatedData(string $client, EntitySchema $schema, AggregationConfiguration $aggregationConfiguration, FilterContainer $filterContainer = null, array $options = []): mixed {
    $queryBuilder = $this->buildQueryFromSchema($schema, $client);

    $this->sqlFilterBuilder->buildFilteredQueryMultipleFilters($queryBuilder, $filterContainer, $schema);
    return $this->aggregationBuilder->fetchAggregatedData($schema, $queryBuilder, $aggregationConfiguration, $options);
  }

}