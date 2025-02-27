<?php

namespace App\Database\AggregationHandler;

use App\Database\AggregationHandler\Attribute\AggregationDefinitionInterface;
use App\Database\AggregationHandler\Attribute\CountAggregationHandlerDefinition;
use App\DataCollections\TableData;
use App\DaViEntity\EntityDataMapperInterface;
use App\DaViEntity\Schema\EntitySchema;
use App\Database\QueryBuilder\QueryBuilderInterface;

class CountAggregationHandler extends AbstractAggregationHandler {

  private const string COUNT_COLUMN = 'count_column';

  public function buildAggregatedQueryBuilder(EntitySchema $schema, QueryBuilderInterface $queryBuilder, AggregationDefinitionInterface $aggregationDefinition, array $options = []): void {
    $queryBuilder->select('COUNT(*) AS ' . self::COUNT_COLUMN);
  }

  public function processingAggregatedData(QueryBuilderInterface $queryBuilder, EntitySchema $schema, AggregationDefinitionInterface $aggregationDefinition): TableData | int {
    if(!$aggregationDefinition instanceof CountAggregationHandlerDefinition) {
      return $this->createEmptyTableData();
    }

    return $this->executeQueryBuilder($queryBuilder, [EntityDataMapperInterface::OPTION_FETCH_TYPE => EntityDataMapperInterface::FETCH_TYPE_ONE], 0);
  }

}
