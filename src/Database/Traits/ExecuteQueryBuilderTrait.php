<?php

namespace App\Database\Traits;

use App\Database\DaViQueryBuilder;
use App\DaViEntity\EntityDataMapperInterface;

trait ExecuteQueryBuilderTrait {

  protected function executeQueryBuilderInternal(DaViQueryBuilder $queryBuilder, array $options = []): mixed {
    $options = array_merge([
      EntityDataMapperInterface::OPTION_FETCH_TYPE => EntityDataMapperInterface::FETCH_TYPE_ASSOCIATIVE,
    ],
      $options
    );

    return match ($options[EntityDataMapperInterface::OPTION_FETCH_TYPE]) {
      EntityDataMapperInterface::FETCH_TYPE_KEY_VALUE => $queryBuilder->fetchAllKeyValue(),
      EntityDataMapperInterface::FETCH_TYPE_ONE => $queryBuilder->fetchOne(),
      EntityDataMapperInterface::FETCH_TYPE_ASSOCIATIVE_INDEXED => $queryBuilder->fetchAllAssociativeIndexed(),
      EntityDataMapperInterface::FETCH_TYPE_ASSOCIATIVE_GROUP_INDEXED => $queryBuilder->fetchAllAssociativeGroupIndexed(),
      default => $queryBuilder->fetchAllAssociative(),
    };
  }

}