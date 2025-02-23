<?php

namespace App\Database\AggregationHandler;

use App\Database\AggregationHandler\Attribute\AggregationDefinitionInterface;
use App\Database\AggregationHandler\Attribute\CountAggregationHandlerDefinition;
use App\Database\DaViQueryBuilder;
use App\DataCollections\TableData;
use App\DaViEntity\EntityDataMapperInterface;
use App\DaViEntity\Schema\EntitySchema;

class CountAggregationHandler extends AbstractAggregationHandler {

  private const string COUNT_COLUMN = 'count_column';

  public function buildAggregatedQueryBuilder(EntitySchema $schema, DaViQueryBuilder $queryBuilder, AggregationDefinitionInterface $aggregationDefinition, array $options = []): void {
    $queryBuilder->select('COUNT(*) AS ' . self::COUNT_COLUMN);
  }

  public function processingAggregatedData(DaViQueryBuilder $queryBuilder, EntitySchema $schema, AggregationDefinitionInterface $aggregationDefinition): TableData {
    if(!$aggregationDefinition instanceof CountAggregationHandlerDefinition) {
      return $this->createEmptyTableData();
    }

    $count = $this->executeQueryBuilder($queryBuilder, [EntityDataMapperInterface::OPTION_FETCH_TYPE => EntityDataMapperInterface::FETCH_TYPE_ONE], 0);

    return TableData::create(
      [self::COUNT_COLUMN => $aggregationDefinition->getLabelCountColumn()],
      [[self::COUNT_COLUMN =>  $count]],
    );
  }

}
