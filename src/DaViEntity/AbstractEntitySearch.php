<?php

namespace App\DaViEntity;

use App\Database\DaViQueryBuilder;
use App\Database\Traits\ExecuteQueryBuilderTrait;
use App\DaViEntity\Schema\EntitySchema;

abstract class AbstractEntitySearch implements EntitySearchInterface {

  use ExecuteQueryBuilderTrait;

  public function __construct(
    protected readonly EntityDataMapperInterface $dataMapper
  ) {}

  public function getEntityListFromSearchString(string $client, EntitySchema $schema, string $searchString, string $uniqueColumn): array {
    $options = [
      EntityDataMapperInterface::OPTION_WITH_COLUMNS => false,
      EntityDataMapperInterface::OPTION_WITH_JOINS => false,
    ];
    $queryBuilder = $this->dataMapper->buildQueryFromSchema($schema, $client, $options);
    $queryBuilder->select($uniqueColumn . ' AS uniqueKey');
    $this->dataMapper->buildLabelColumn($queryBuilder, $schema);
    $this->dataMapper->buildEntityKeyColumn($queryBuilder, $schema);
    $this->buildWhere($queryBuilder, $schema, $searchString);
    return $this->executeQueryBuilderInternal($queryBuilder);
  }

  protected function buildWhere(DaViQueryBuilder $queryBuilder, EntitySchema $schema, string $searchString): void {
    $searchProperties = $schema->getSearchProperties();
    $queryBuilder->setMaxResults(15);

    if(empty($searchString)) {
      return;
    }

    $innerExpressions = [];

    foreach ($searchProperties as $property) {
      $column = $schema->getColumn($property);

      if(empty($column)) {
        continue;
      }

      $innerExpressions[] = $queryBuilder->expr()->like($column, ':search_string');
    }

    $queryBuilder->andWhere($queryBuilder->expr()->or(...$innerExpressions));

    $queryBuilder->setParameter('search_string', '%' . $searchString . '%');
  }

}