<?php

namespace App\EntityServices\AggregatedData;

use App\Database\AggregationHandler\Attribute\AggregationDefinitionInterface;
use App\Database\SqlFilter\FilterContainer;
use App\DataCollections\TableData;

class NullAggregatedDataProvider implements AggregatedDataProviderInterface {

  public function fetchAggregatedData(
    string $entityClass,
    string $client,
    AggregationDefinitionInterface $aggregationDefinition,
    FilterContainer $filterContainer = null,
    array $options = []
  ): TableData {
    return TableData::createEmptyTableData();
  }

}