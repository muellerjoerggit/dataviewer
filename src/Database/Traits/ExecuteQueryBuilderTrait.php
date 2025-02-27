<?php

namespace App\Database\Traits;

use App\Database\QueryBuilder\DaViQueryBuilder;
use App\Database\QueryBuilder\QueryBuilderInterface;
use App\DaViEntity\EntityDataMapperInterface;
use Doctrine\DBAL\Exception;

trait ExecuteQueryBuilderTrait {

  protected function executeQueryBuilderInternal(QueryBuilderInterface $queryBuilder, array $options = [], $default = null): mixed {
    $options = array_merge([
      EntityDataMapperInterface::OPTION_FETCH_TYPE => EntityDataMapperInterface::FETCH_TYPE_ALL_ASSOCIATIVE,
    ],
      $options
    );

    $ret = $default;
    try {
      $ret = match ($options[EntityDataMapperInterface::OPTION_FETCH_TYPE]) {
        EntityDataMapperInterface::FETCH_TYPE_KEY_VALUE => $queryBuilder->fetchAllKeyValue(),
        EntityDataMapperInterface::FETCH_TYPE_ONE => $queryBuilder->fetchOne(),
        EntityDataMapperInterface::FETCH_TYPE_ASSOCIATIVE_INDEXED => $queryBuilder->fetchAllAssociativeIndexed(),
        EntityDataMapperInterface::FETCH_TYPE_ASSOCIATIVE_GROUP_INDEXED => $queryBuilder->fetchAllAssociativeGroupIndexed(),
        EntityDataMapperInterface::FETCH_TYPE_ASSOCIATIVE => $queryBuilder->fetchAssociative(),
        default => $queryBuilder->fetchAllAssociative(),
      };
    } catch (Exception $exception) {

    }

    return $ret;
  }

}