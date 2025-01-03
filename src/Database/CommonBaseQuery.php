<?php

namespace App\Database;

use App\DaViEntity\EntityDataMapperInterface;
use App\DaViEntity\Schema\EntitySchema;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;

class CommonBaseQuery {

  public function __construct(
    private readonly DatabaseLocator $databaseLocator,
    private readonly EntityTypeSchemaRegister $entityTypeSchemaRegister
  ) {}

  public function buildQueryFromSchema(string $entityTypeClass, string $client, array $options = []): DaViQueryBuilder {
    $options = $this->getDefaultQueryOptions($options);
    $schema = $this->entityTypeSchemaRegister->getSchemaFromEntityClass($entityTypeClass);

    $baseTable = $schema->getBaseTable();
    if ($options[EntityDataMapperInterface::OPTION_WITH_COLUMNS]) {
      $columns = $schema->getColumns();
    }
    else {
      $columns = [];
    }

    $queryBuilder = $this->getQueryBuilder($schema, $client);

    foreach ($columns as $property => $column) {
      $queryBuilder->addSelect($column . ' AS ' . $property);
    }


    $queryBuilder->from($baseTable);
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

  protected function getQueryBuilder(EntitySchema $schema, string $client): DaViQueryBuilder {
    return $this->databaseLocator->getDatabaseBySchema($schema)->createQueryBuilder($client);
  }
  
}