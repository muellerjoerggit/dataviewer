<?php

namespace App\Database\AggregationHandler;

use App\Database\Aggregation\AggregationHandlerInterface;
use App\Database\QueryBuilder\QueryBuilderInterface;
use App\Database\Traits\ExecuteQueryBuilderTrait;
use App\DataCollections\TableData;
use Exception;

abstract class AbstractAggregationHandler implements AggregationHandlerInterface {

  use ExecuteQueryBuilderTrait;

  protected function executeQueryBuilder(QueryBuilderInterface $queryBuilder, array $options = [], mixed $default = []): mixed {
    try {
      return $this->executeQueryBuilderInternal($queryBuilder, $options);
    } catch (Exception $exception) {
      return $default;
    }
  }

  protected function createEmptyTableData(): TableData {
    return TableData::createEmptyTableData();
  }

  protected function mergeDefaultOptions(array $options): array {
    return array_merge([
        AggregationHandlerInterface::OPTION_PROPERTY_BLACKLIST => [],
      ],
      $options
    );
  }

}