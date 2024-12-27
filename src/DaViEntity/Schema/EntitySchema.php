<?php

namespace App\DaViEntity\Schema;

use App\Database\Aggregation\AggregationConfiguration;
use App\Database\SqlFilter\FilterContainer;
use App\Database\SqlFilter\FilterGroup;
use App\Database\SqlFilter\SqlFilterDefinition;
use App\Database\SqlFilter\SqlFilterDefinitionInterface;
use App\Database\SqlFilterHandler\NullFilterHandler;
use App\Database\TableReference\TableReferenceConfiguration;
use App\Item\ItemInterface;
use App\Item\Property\PropertyConfiguration;

class EntitySchema implements EntitySchemaInterface {

  private string $entityLabel;
  private string $entityType;
  private string $description;

  private string $baseTable;
  private array $columns = [];
  private int $database;

  private array $properties;

  private array $filters = [];
  private array $defaultFilters = [];
  private array $mandatoryFilters = [];
  private array $generatedFilters = [];
  private array $filterGroups = [];
  private array $groupFilterMapping = [];

  private array $tableReferenceColumns = [];
  private array $tableReferences = [];

  private array $aggregations = [];

  private array $uniqueIdentifiers;
  private array $entityLabelProperties = [];
  private array $searchProperties = [];

  private array $entityOverview = [];
  private array $extendedEntityOverview = [];

  public function __construct() {
    $filterGroup = new FilterGroup(EntitySchemaInterface::WITHOUT_FILTER_GROUP);
    $this->addFilterGroup($filterGroup);
  }

  public function getDatabase(): int {
    return $this->database;
  }

  public function setDatabase(int $database): EntitySchema {
    $this->database = $database;
    return $this;
  }

  public function getEntityLabel(): string {
    return $this->entityLabel;
  }

  public function setEntityLabel(string $entityLabel): EntitySchemaInterface {
    $this->entityLabel = $entityLabel;
    return $this;
  }

  public function getEntityType(): string {
    return $this->entityType;
  }

  public function setEntityType(string $entityType): EntitySchemaInterface {
    $this->entityType = $entityType;
    return $this;
  }

  public function getDescription(): string {
    return $this->description ?? '';
  }

  public function setDescription(string $description): EntitySchema {
    $this->description = $description;
    return $this;
  }

  public function getBaseTable(): string {
    return $this->baseTable;
  }

  public function setBaseTable(string $baseTable): EntitySchemaInterface {
    $this->baseTable = $baseTable;
    return $this;
  }

  public function getColumns(): array {
    return $this->columns;
  }

  public function getColumn(string $property): string {
    return $this->columns[$property] ?? '';
  }

  public function addProperty(PropertyConfiguration $property): EntitySchemaInterface {
    $name = $property->getItemName();
    $this->properties[$name] = $property;
    if($property->hasColumn() && !$property->hasTableReference()) {
      $this->columns[$name] = $property->getColumn();
    }
    return $this;
  }

  public function getProperty(string $property): PropertyConfiguration {
    return $this->properties[$property];
  }

  public function hasProperty(string $property): bool {
    return isset($this->properties[$property]);
  }

  /**
   * @return \Generator<PropertyConfiguration>
   */
  public function iterateProperties(): \Generator {
    foreach ($this->properties as $key => $config) {
      yield $key => $config;
    }
  }

  public function getDefaultFiltersAsContainer(string $client): FilterContainer {
    return new FilterContainer($client, $this->defaultFilters);
  }

  public function getMandatoryFiltersAsContainer(string $client): FilterContainer {
    return new FilterContainer($client, $this->mandatoryFilters);
  }

  public function hasMandatoryFilters(): bool {
    return !empty($this->mandatoryFilters);
  }

  public function hasFilter(string $filterKey): bool {
    return isset($this->filters[$filterKey]);
  }

  public function addFilter(SqlFilterDefinitionInterface $filterDefinition, string $property = '', ?FilterGroup $filterGroup = null): EntitySchemaInterface {
    $groupKey = EntitySchemaInterface::WITHOUT_FILTER_GROUP;

    $filterKey = $filterDefinition->getKey();
    $this->filters[$filterKey] = $filterDefinition;

    if($filterGroup && !empty($filterKey)) {
      $groupKey = $filterGroup->getGroupKey();
      $this->addFilterGroup($filterGroup);
    }

    $this->groupFilterMapping[$groupKey][] = $filterKey;

    return $this;
  }

  public function getFilterDefinition(string $filterKey): SqlFilterDefinitionInterface {
    return $this->filters[$filterKey] ?? NullFilterHandler::getNullFilterDefinition();
  }

  /**
   * @return \Generator<SqlFilterDefinition>
   */
  public function iterateFilterDefinitions(): \Generator {
    foreach ($this->filters as $key => $config) {
      yield $key => $config;
    }
  }

  public function getGeneratedFilterProperty(string $filterKey): string {
    return $this->generatedFilters[$filterKey] ?? '';
  }

  /**
   * @return \Generator<FilterGroup>
   */
  public function iterateFilterGroups(): \Generator {
    foreach ($this->filterGroups as $key => $group) {
      yield $key => $group;
    }
  }

  public function addFilterGroup(FilterGroup $filterGroup): EntitySchemaInterface {
    $groupKey = $filterGroup->getGroupKey();

    if(!$this->hasFilterGroup($groupKey)) {
      $this->filterGroups[$groupKey] = $filterGroup;
    }

    return $this;
  }

  public function hasFilterGroup(string $groupKey): bool {
    return isset($this->filterGroups[$groupKey]);
  }

  public function getGroupFilterMappings(): array {
    return $this->groupFilterMapping;
  }


  public function getUniqueProperties(): array {
    return $this->uniqueIdentifiers ?? [];
  }

  public function getFirstUniqueProperties(): array {
    $uniqueProperties = $this->uniqueIdentifiers;
    return reset($uniqueProperties);
  }

  public function setUniqueProperties(array $uniqueIdentifiers): EntitySchemaInterface {
    $this->uniqueIdentifiers = $uniqueIdentifiers;
    return $this;
  }

  public function getEntityLabelProperties(): array {
    return $this->entityLabelProperties;
  }

  public function setEntityLabelProperties(array $entityLabelProperties): EntitySchemaInterface	{
    $this->entityLabelProperties = $entityLabelProperties;
    return $this;
  }

  public function getEntityOverviewProperties(): array {
    return $this->entityOverview;
  }

  public function setEntityOverviewProperties(array $entityOverview): EntitySchemaInterface {
    $this->entityOverview = $entityOverview;
    return $this;
  }

  public function getExtendedEntityOverviewProperties(): array {
    return !empty($this->extendedEntityOverview) ? $this->extendedEntityOverview : $this->entityOverview;
  }

  public function setExtendedEntityOverviewProperties(array $extendedEntityOverview): EntitySchemaInterface {
    $this->extendedEntityOverview = $extendedEntityOverview;
    return $this;
  }

  public function isSingleColumnPrimaryKeyInteger(): bool {
    $firstUniqueProperties = $this->getFirstUniqueProperties();
    if(count($firstUniqueProperties) != 1) {
      return false;
    }

    $config = $this->getProperty(reset($firstUniqueProperties));
    return $config->getDataType() === ItemInterface::DATA_TYPE_INTEGER;
  }

  public function addAggregation(AggregationConfiguration $aggregationConfiguration): EntitySchemaInterface {
    $name = $aggregationConfiguration->getName();
    $this->aggregations[$name] = $aggregationConfiguration;
    return $this;
  }

  public function hasAggregations(): bool {
    return !empty($this->aggregations);
  }

  public function getAggregation(string $name): ?AggregationConfiguration {
    if(isset($this->aggregations[$name])) {
      return $this->aggregations[$name];
    }

    return null;
  }

  /**
   * @return \Generator<AggregationConfiguration>
   */
  public function iterateAggregations(): \Generator {
    foreach($this->aggregations as $key => $aggregation) {
      yield $key => $aggregation;
    }
  }

  public function getSearchProperties(): array {
    return $this->searchProperties;
  }

  public function setSearchProperties(array $searchProperties): EntitySchemaInterface {
    $this->searchProperties = $searchProperties;
    return $this;
  }

  public function addTableReference(TableReferenceConfiguration $tableReferenceConfiguration, string $internalName): EntitySchemaInterface {
    $this->tableReferences[$internalName] = $tableReferenceConfiguration;
    return $this;
  }

  public function getTableReference(string $internalName): TableReferenceConfiguration {
    return $this->tableReferences[$internalName] ?? TableReferenceConfiguration::createNullConfig($this->entityType . '-' . $internalName);
  }

  public function iterateTableReferences(): \Generator {
    foreach($this->tableReferences as $internalName => $tableReferenceConfiguration) {
      yield $internalName => $tableReferenceConfiguration;
    }
  }

  public function addTableReferenceColumn(string $tableReferenceInternalName, string $column, string $property): EntitySchemaInterface {
    $this->tableReferenceColumns[$tableReferenceInternalName][$property] = $column;
    return $this;
  }

  /**
   * @return array<string>
   */
  public function getTableReferenceColumns(string $tableReferenceInternalName): array {
    return $this->tableReferenceColumns[$tableReferenceInternalName] ?? [];
  }

}