<?php

namespace App\EntityServices\AggregatedData;

use App\Database\Aggregation\AggregationConfiguration;
use App\Database\SqlFilter\FilterContainer;
use App\DataCollections\TableData;

class NullAggregatedDataProvider implements AggregatedDataProviderInterface {

  public function fetchAggregatedData(
    string $entityClass,
    string $client,
    AggregationConfiguration $aggregationConfiguration,
    FilterContainer $filterContainer = null,
    array $options = []
  ): TableData {
    return new TableData([], []);
  }

}