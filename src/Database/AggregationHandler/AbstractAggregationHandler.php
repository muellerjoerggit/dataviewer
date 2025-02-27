<?php

namespace App\Database\AggregationHandler;

use App\Database\Aggregation\AggregationHandlerInterface;
use App\Database\QueryBuilder\DaViQueryBuilder;
use App\Database\QueryBuilder\QueryBuilderInterface;
use App\Database\Traits\ExecuteQueryBuilderTrait;
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

}