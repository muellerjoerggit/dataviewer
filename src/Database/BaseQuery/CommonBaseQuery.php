<?php

namespace App\Database\BaseQuery;

use App\Database\DatabaseLocator;
use App\Database\QueryBuilder\DaViQueryBuilder;
use App\Database\QueryBuilder\QueryBuilderInterface;
use App\DaViEntity\EntityDataMapperInterface;
use App\DaViEntity\EntityInterface;
use App\DaViEntity\Schema\EntitySchema;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use App\DaViEntity\Schema\VersionProperties;
use App\Item\Property\PropertyConfiguration;

class CommonBaseQuery implements BaseQueryInterface {

  public function __construct(
    private readonly DatabaseLocator $databaseLocator,
    private readonly EntityTypeSchemaRegister $entityTypeSchemaRegister,
    private readonly VersionProperties $versionsProperties,
  ) {}

  public function buildQueryFromSchema(string | EntityInterface $entityTypeClass, string $client, array $options = []): QueryBuilderInterface {
    $options = $this->getDefaultQueryOptions($options);
    $schema = $this->entityTypeSchemaRegister->getSchemaFromEntityClass($entityTypeClass);

    $queryBuilder = $this->getQueryBuilder($schema, $client);

    $columns = $this->getColumnsFromOptions($schema, $options);
    $columns = $this->versionsProperties->filterPropertyKeysByVersion($entityTypeClass, $columns, $client);

    foreach ($columns as $property => $column) {
      if($column instanceof PropertyConfiguration) {
        $column = $column->getColumn();
      }

      $queryBuilder->addSelect($column . ' AS ' . $property);
    }

    $queryBuilder->from($schema->getBaseTable());
    $queryBuilder->setMaxResults($options[EntityDataMapperInterface::OPTION_LIMIT]);

    return $queryBuilder;
  }

  protected function getDefaultQueryOptions(array $options): array {
    return array_merge(
      [
        EntityDataMapperInterface::OPTION_WITH_COLUMNS => true,
        EntityDataMapperInterface::OPTION_WITH_JOINS => true,
        EntityDataMapperInterface::OPTION_LIMIT => 50
      ],
      $options
    );
  }

  protected function getQueryBuilder(EntitySchema $schema, string $client): QueryBuilderInterface {
    return $this->databaseLocator->getDatabaseBySchema($schema)->createQueryBuilder($client);
  }

  private function getColumnsFromOptions(EntitySchema $schema, array $options): array {
    if (
      $options[EntityDataMapperInterface::OPTION_WITH_COLUMNS]
      && empty($options[EntityDataMapperInterface::OPTION_COLUMNS])
    ) {
      $columns = $schema->getColumns();
    } elseif (
      $options[EntityDataMapperInterface::OPTION_WITH_COLUMNS]
      && !empty($options[EntityDataMapperInterface::OPTION_COLUMNS])
    ) {
      $columns = $options[EntityDataMapperInterface::OPTION_COLUMNS];
    } else {
      $columns = [];
    }

    return $columns;
  }
  
}