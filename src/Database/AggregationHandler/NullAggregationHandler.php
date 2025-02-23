<?php

namespace App\Database\AggregationHandler;

use App\Database\Aggregation\AggregationConfiguration;
use App\Database\Aggregation\AggregationHandlerInterface;
use App\Database\DaViQueryBuilder;
use App\DataCollections\TableData;
use App\DaViEntity\Schema\EntitySchema;

class NullAggregationHandler implements AggregationHandlerInterface {

  public function processingAggregatedData(DaViQueryBuilder $queryBuilder, EntitySchema $schema, AggregationConfiguration $aggregationConfiguration): TableData {
    return new TableData([], []);
  }

  public function buildAggregatedQueryBuilder(
    EntitySchema $schema,
    DaViQueryBuilder $queryBuilder,
    AggregationConfiguration $aggregationConfiguration,
    array $options = []
  ): void {}

}
