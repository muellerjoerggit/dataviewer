<?php

namespace App\DaViEntity\ListSearch;

use App\Database\BaseQuery\BaseQueryLocator;
use App\Database\DaViQueryBuilder;
use App\Database\Traits\ExecuteQueryBuilderTrait;
use App\DaViEntity\ColumnBuilder\EntityColumnBuilderLocator;
use App\DaViEntity\EntityDataMapperInterface;
use App\DaViEntity\EntityInterface;
use App\DaViEntity\Schema\EntitySchema;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;

class CommonEntitySearch implements EntityListSearchInterface {

  use ExecuteQueryBuilderTrait;

  public function __construct(
    protected readonly EntityDataMapperInterface $dataMapper,
    protected readonly EntityTypeSchemaRegister $entityTypeSchemaRegister,
    protected readonly BaseQueryLocator $queryLocator,
    protected readonly EntityColumnBuilderLocator $entityColumnBuilderLocator,
  ) {}

  public function getEntityListFromSearchString(string | EntityInterface $entityClass, string $client, string $searchString): array {
    if($entityClass instanceof EntityInterface) {
      $entityClass = get_class($entityClass);
    }

    $options = [
      EntityDataMapperInterface::OPTION_WITH_COLUMNS => false,
      EntityDataMapperInterface::OPTION_WITH_JOINS => false,
    ];

    $schema = $this->entityTypeSchemaRegister->getSchemaFromEntityClass($entityClass);

    $uniqueProperties = $schema->getUniqueProperties();
    $uniqueProperty = reset($uniqueProperties);
    $uniqueProperty = reset($uniqueProperty);
    $uniqueColumn = $schema->getColumn($uniqueProperty);

    $queryBuilder = $this->queryLocator->getBaseQuery($entityClass)->buildQueryFromSchema($entityClass, $client, $options);
    $queryBuilder->select($uniqueColumn . ' AS uniqueKey');

    $entityColumnBuilder = $this->entityColumnBuilderLocator->getEntityColumnBuilder($entityClass);

    $entityColumnBuilder->buildLabelColumn($queryBuilder, $entityClass);
    $entityColumnBuilder->buildEntityKeyColumn($queryBuilder, $entityClass);

    $this->buildWhere($queryBuilder, $schema, $searchString);
    return $this->executeQueryBuilderInternal($queryBuilder);
  }

  protected function buildWhere(DaViQueryBuilder $queryBuilder, EntitySchema $schema, string $searchString): void {
    $searchProperties = $schema->getSearchProperties();
    $queryBuilder->setMaxResults(15);

    if(empty($searchString) || empty($searchProperties)) {
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