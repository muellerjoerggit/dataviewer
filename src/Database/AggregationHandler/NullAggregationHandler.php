<?php

namespace App\Database\AggregationHandler;

use App\Database\Aggregation\AggregationHandlerInterface;
use App\Database\AggregationHandler\Attribute\AggregationDefinitionInterface;
use App\Database\DaViQueryBuilder;
use App\DataCollections\TableData;
use App\DaViEntity\Schema\EntitySchema;

class NullAggregationHandler implements AggregationHandlerInterface {

  public function processingAggregatedData(DaViQueryBuilder $queryBuilder, EntitySchema $schema, AggregationDefinitionInterface $aggregationDefinition): TableData {
    return TableData::createEmptyTableData();
  }

  public function buildAggregatedQueryBuilder(
    EntitySchema $schema,
    DaViQueryBuilder $queryBuilder,
    AggregationDefinitionInterface $aggregationDefinition,
    array $options = []
  ): void {}

}
