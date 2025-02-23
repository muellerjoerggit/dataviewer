<?php

namespace App\Database\Aggregation;

use App\Database\DaViQueryBuilder;
use App\DataCollections\TableData;
use App\DaViEntity\Schema\EntitySchema;

class AggregationBuilder {

  public function __construct(
    private readonly AggregationHandlerLocator $aggregationHandlerLocator
  ) {}

  public function fetchAggregatedData(EntitySchema $schema, DaViQueryBuilder $queryBuilder, AggregationConfiguration $aggregationConfiguration, array $options = []): TableData {
    $handler = $this->aggregationHandlerLocator->getAggregationHandler($aggregationConfiguration);
    $handler->buildAggregatedQueryBuilder($schema, $queryBuilder, $aggregationConfiguration, $options);
    return $handler->processingAggregatedData($queryBuilder, $schema, $aggregationConfiguration);
  }

}
