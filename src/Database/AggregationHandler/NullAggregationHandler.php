<?php

namespace App\Database\AggregationHandler;

use App\Database\Aggregation\AggregationHandlerInterface;
use App\Database\AggregationHandler\Attribute\AggregationDefinitionInterface;
use App\Database\QueryBuilder\QueryBuilderInterface;
use App\DataCollections\TableData;
use App\DaViEntity\Schema\EntitySchema;

class NullAggregationHandler implements AggregationHandlerInterface {

  public function processingAggregatedData(QueryBuilderInterface $queryBuilder, EntitySchema $schema, AggregationDefinitionInterface $aggregationDefinition): TableData | int {
    return TableData::createEmptyTableData();
  }

  public function buildAggregatedQueryBuilder(
    EntitySchema $schema,
    QueryBuilderInterface $queryBuilder,
    AggregationDefinitionInterface $aggregationDefinition,
    array $options = []
  ): void {}

}

