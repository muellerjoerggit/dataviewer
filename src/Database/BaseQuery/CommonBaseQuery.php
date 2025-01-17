<?php

namespace App\Database\BaseQuery;

use App\Database\ColumnsService;
use App\Database\DatabaseLocator;
use App\Database\DaViQueryBuilder;
use App\DaViEntity\EntityDataMapperInterface;
use App\DaViEntity\EntityInterface;
use App\DaViEntity\Schema\EntitySchema;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;

class CommonBaseQuery implements BaseQueryInterface {

  public function __construct(
    private readonly DatabaseLocator $databaseLocator,
    private readonly EntityTypeSchemaRegister $entityTypeSchemaRegister,
    private readonly ColumnsService $columnsService,
  ) {}

  public function buildQueryFromSchema(string | EntityInterface $entityTypeClass, string $client, array $options = []): DaViQueryBuilder {
    $options = $this->getDefaultQueryOptions($options);
    $schema = $this->entityTypeSchemaRegister->getSchemaFromEntityClass($entityTypeClass);

    $queryBuilder = $this->getQueryBuilder($schema, $client);
    $columns = $this->columnsService->getColumns($entityTypeClass, $client, $options);

    foreach ($columns as $property => $column) {
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

  protected function getQueryBuilder(EntitySchema $schema, string $client): DaViQueryBuilder {
    return $this->databaseLocator->getDatabaseBySchema($schema)->createQueryBuilder($client);
  }
  
}