<?php

namespace App\Database\AggregationHandler;

use App\Database\Aggregation\AggregationConfiguration;
use App\Database\Aggregation\AggregationHandlerInterface;
use App\Database\DaViQueryBuilder;
use App\DataCollections\TableData;
use App\DaViEntity\EntityDataMapperInterface;
use App\DaViEntity\Schema\EntitySchema;

class CountAggregationHandler extends AbstractAggregationHandler {

  public function buildAggregatedQueryBuilder(EntitySchema $schema, DaViQueryBuilder $queryBuilder, AggregationConfiguration $aggregationConfiguration, array $options = []): void {
    $queryBuilder->select('COUNT(*) AS ' . AggregationHandlerInterface::YAML_PARAM_COUNT_COLUMN);
  }

  public function processingAggregatedData(DaViQueryBuilder $queryBuilder, EntitySchema $schema, AggregationConfiguration $aggregationConfiguration): mixed {
    $data = $this->executeQueryBuilder($queryBuilder, [EntityDataMapperInterface::OPTION_FETCH_TYPE => EntityDataMapperInterface::FETCH_TYPE_ONE], 0);

    $headerColumns = $aggregationConfiguration->getSetting('header');
    $headerColumns[AggregationHandlerInterface::YAML_PARAM_COUNT_COLUMN] = $headerColumns[AggregationHandlerInterface::YAML_PARAM_COUNT_COLUMN] ?? 'Anzahl';

    return new TableData($headerColumns, [[AggregationHandlerInterface::YAML_PARAM_COUNT_COLUMN => $data]]);
  }

}
