<?php

namespace App\DaViEntity;

use App\Database\Aggregation\AggregationBuilder;
use App\Database\Aggregation\AggregationConfiguration;
use App\Database\DatabaseLocator;
use App\Database\DaViQueryBuilder;
use App\Database\SqlFilter\FilterContainer;
use App\Database\SqlFilter\SqlFilterBuilder;
use App\Database\Traits\ExecuteQueryBuilderTrait;
use App\DataCollections\EntityList;
use App\DaViEntity\Schema\EntitySchema;

class CommonEntityDataMapper implements EntityDataMapperInterface {

  use ExecuteQueryBuilderTrait;

  public function __construct(
    private readonly DatabaseLocator $databaseLocator,
    private readonly SqlFilterBuilder $sqlFilterBuilder,
    private readonly AggregationBuilder $aggregationBuilder,
  ) {}

  protected function getQueryBuilder(EntitySchema $schema, string $client): DaViQueryBuilder {
    return $this->databaseLocator->getDatabaseBySchema($schema)->createQueryBuilder($client);
  }

  public function buildQueryFromSchema(EntitySchema $schema, string $client, array $options = []): DaViQueryBuilder {
    $options = $this->getDefaultQueryOptions($options);

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

  protected function executeQueryBuilder(DaViQueryBuilder $queryBuilder, array $options = []): mixed {
    try {
      return $this->executeQueryBuilderInternal($queryBuilder, $options);
    } catch (\Exception $exception) {
      return [];
    }
  }

  public function fetchEntityData(EntitySchema $schema, FilterContainer $filters, array $options = []): array {
    $client = $filters->getClient();
    $queryBuilder = $this->buildQueryFromSchema($schema, $client);

    $this->sqlFilterBuilder->buildFilteredQueryMultipleFilters($queryBuilder, $filters, $schema);
    $queryBuilder->setMaxResults($filters->getLimit());

    return $this->executeQueryBuilder($queryBuilder);
  }

  public function fetchAggregatedData(
    string $client,
    EntitySchema $schema,
    AggregationConfiguration $aggregationConfiguration,
    FilterContainer $filterContainer = null,
    array $columnsBlacklist = []
  ): mixed {
    $queryBuilder = $this->buildQueryFromSchema($schema, $client);

    $this->sqlFilterBuilder->buildFilteredQueryMultipleFilters($queryBuilder, $filterContainer, $schema);
    return $this->aggregationBuilder->fetchAggregatedData($schema, $queryBuilder, $aggregationConfiguration, $columnsBlacklist);
  }

  public function getEntityList(EntitySchema $schema, FilterContainer $filterContainer): EntityList {
    $client = $filterContainer->getClient();

    $queryBuilder = $this->buildQueryFromSchema($schema, $client, [EntityDataMapperInterface::OPTION_WITH_COLUMNS => false]);

    $this->buildLabelColumn($queryBuilder, $schema, true);
    $this->buildEntityKeyColumn($queryBuilder, $schema);

    $queryBuilder->setMaxResults($filterContainer->getLimit());

    $this->sqlFilterBuilder->buildFilteredQueryMultipleFilters($queryBuilder, $filterContainer, $schema);

    $countQueryBuilder = clone $queryBuilder;

    $queryResult = $this->executeQueryBuilder($queryBuilder);
    $database = $this->databaseLocator->getDatabaseBySchema($schema);
    $entityCount = $database->getCountResultFromQueryBuilder($countQueryBuilder, EntityDataMapperInterface::FETCH_TYPE_ONE);

    if(!is_integer($entityCount)) {
      $entityCount = -1;
    }

    $list = new EntityList();
    $list
      ->setUseBound($schema->isSingleColumnPrimaryKeyInteger())
      ->setTotalCount($entityCount)
      ->addEntities($queryResult)
    ;

    return $list;
  }

  public function buildLabelColumn(DaViQueryBuilder $queryBuilder, EntitySchema $schema, bool $withEntityLabel = false): void {
    $concat = '';
    $entityLabelProperties = $schema->getEntityLabelProperties();
    foreach ($entityLabelProperties as $labelProperty) {
      $column = $schema->getColumn($labelProperty);

      if(empty($column)) {
        continue;
      }

      $concat = empty($concat) ? $column : $concat . ',' . $column;
    }
    $concat = $withEntityLabel ? '"' . $schema->getEntityLabel() . ':",' . $concat : $concat;
    $queryBuilder->addSelect('SUBSTRING(CONCAT_WS(" ", ' . $concat . '), 1, 50) AS entityLabel');
  }

  public function buildEntityKeyColumn(DaViQueryBuilder $queryBuilder, EntitySchema $schema): void {
    $concat = '';
    $client = $queryBuilder->getClient();

    $uniquePropertyArray = $schema->getFirstUniqueProperties();
    $uniqueProperty = implode('+', $uniquePropertyArray);

    foreach ($uniquePropertyArray as $property) {
      $column = $schema->getColumn($property);
      $concat = empty($concat) ? $column : $concat . ',"+",' . $column;
    }
    $entityType = $schema->getEntityType();
    $uniqueKey = 'CONCAT_WS("", ' . $concat . ')';

    $queryBuilder->addSelect('CONCAT_WS("", "' . $client . '", "::","' . $entityType . '", "::","' . $uniqueProperty . '", "::",' . $concat . ') AS entityKey');
    $queryBuilder->addSelect($uniqueKey . ' AS uniqueKey');
    $queryBuilder->orderBy($uniqueKey);
  }

}