<?php

namespace App\DaViEntity\Schema;

use App\Database\Aggregation\AggregationConfiguration;
use App\Database\SqlFilter\FilterContainer;
use App\Database\SqlFilter\FilterGroup;
use App\Database\SqlFilter\SqlFilterDefinition;
use App\Database\SqlFilter\SqlFilterDefinitionInterface;
use App\Database\SqlFilter\SqlGeneratedFilterDefinition;
use App\Database\SqlFilterHandler\NullFilterHandler;
use App\Item\ItemInterface;
use App\Item\Property\PropertyConfiguration;
use Generator;

class EntitySchema implements EntitySchemaInterface {

  private string $entityLabel;

  private string $entityType;

  private string $description;

  private string $baseTable;

  private array $columns = [];

  private array $properties;

  private array $filters = [];

  private array $defaultFilters = [];

  private array $mandatoryFilters = [];

  private array $generatedFilters = [];

  private array $filterGroups = [];

  private array $groupFilterMapping = [];

  private array $aggregations = [];

  private array $uniqueIdentifiers;

  private array $entityLabelProperties = [];

  private array $entityOverview = [];

  private array $extendedEntityOverview = [];

  public function __construct() {
    $filterGroup = new FilterGroup(
      EntitySchemaInterface::WITHOUT_FILTER_GROUP
    );

    $this->addFilterGroup($filterGroup);
  }

  public function addFilterGroup(FilterGroup $filterGroup): EntitySchemaInterface {
    $groupKey = $filterGroup->getGroupKey();

    if (!$this->hasFilterGroup($groupKey)) {
      $this->filterGroups[$groupKey] = $filterGroup;
    }

    return $this;
  }

  public function hasFilterGroup(string $groupKey): bool {
    return isset($this->filterGroups[$groupKey]);
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

  public function addProperty(PropertyConfiguration $property): EntitySchemaInterface {
    $name = $property->getItemName();
    $this->properties[$name] = $property;
    if ($property->hasColumn()) {
      $this->columns[$name] = $property->getColumn();
    }
    return $this;
  }

  public function getColumn(string $property): string {
    return $this->columns[$property] ?? '';
  }

  public function hasProperty(string $property): bool {
    return isset($this->properties[$property]);
  }

  /**
   * @return \Generator<PropertyConfiguration>
   */
  public function iterateProperties(): Generator {
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

  public function addFilter(SqlFilterDefinitionInterface $filterDefinition, string $property = '', ?FilterGroup $filterGroup = NULL): EntitySchemaInterface {
    $filterKey = '';
    $groupKey = EntitySchemaInterface::WITHOUT_FILTER_GROUP;
    if ($filterDefinition instanceof SqlFilterDefinition) {
      $filterKey = SqlFilterDefinitionInterface::FILTER_PREFIX_STANDALONE . '_' . $filterDefinition->getKey();
      $this->filters[$filterKey] = $filterDefinition;
    } elseif ($filterDefinition instanceof SqlGeneratedFilterDefinition && !empty($property)) {
      $filterKey = SqlFilterDefinitionInterface::FILTER_PREFIX_GENERATED . '_' . $property . '_' . $filterDefinition->getKey();
      $this->generatedFilters[$filterKey] = $property;
      $this->filters[$filterKey] = $filterDefinition;
    }

    if ($filterGroup && !empty($filterKey)) {
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
  public function iterateFilterDefinitions(): Generator {
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
  public function iterateFilterGroups(): Generator {
    foreach ($this->filterGroups as $key => $group) {
      yield $key => $group;
    }
  }

  public function getGroupFilterMappings(): array {
    return $this->groupFilterMapping;
  }

  public function getUniqueProperties(): array {
    return $this->uniqueIdentifiers ?? [];
  }

  public function setUniqueProperties(array $uniqueIdentifiers): EntitySchemaInterface {
    $this->uniqueIdentifiers = $uniqueIdentifiers;
    return $this;
  }

  public function getEntityLabelProperties(): array {
    return $this->entityLabelProperties;
  }

  public function setEntityLabelProperties(array $entityLabelProperties): EntitySchemaInterface {
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
    if (count($firstUniqueProperties) != 1) {
      return FALSE;
    }

    $config = $this->getProperty(reset($firstUniqueProperties));
    return $config->getDataType() === ItemInterface::DATA_TYPE_INTEGER;
  }

  public function getFirstUniqueProperties(): array {
    $uniqueProperties = $this->uniqueIdentifiers;
    return reset($uniqueProperties);
  }

  public function getProperty(string $property): PropertyConfiguration {
    return $this->properties[$property];
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
    if (isset($this->aggregations[$name])) {
      return $this->aggregations[$name];
    }

    return NULL;
  }

  /**
   * @return \Generator<AggregationConfiguration>
   */
  public function iterateAggregations(): Generator {
    foreach ($this->aggregations as $key => $aggregation) {
      yield $key => $aggregation;
    }
  }

}