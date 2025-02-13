<?php

namespace App\DaViEntity\Schema;

use App\Database\Aggregation\AggregationConfiguration;
use App\Database\SqlFilter\FilterContainer;
use App\Database\SqlFilter\FilterGroup;
use App\Database\SqlFilter\SqlFilterDefinition;
use App\Database\SqlFilterHandler\Attribute\SqlFilterDefinitionInterface;
use App\Database\SqlFilterHandler\NullFilterHandler;
use App\Database\TableReferenceHandler\Attribute\TableReferenceAttr;
use App\Database\TableReferenceHandler\Attribute\TableReferenceAttrInterface;
use App\DaViEntity\AdditionalData\AdditionalDataProviderDefinitionInterface;
use App\DaViEntity\ColumnBuilder\ColumnBuilderDefinitionInterface;
use App\DaViEntity\Creator\CreatorDefinitionInterface;
use App\DaViEntity\DataProvider\DataProviderDefinitionInterface;
use App\DaViEntity\ListProvider\ListProviderDefinitionInterface;
use App\DaViEntity\Refiner\RefinerDefinitionInterface;
use App\DaViEntity\Repository\RepositoryDefinitionInterface;
use App\DaViEntity\Search\SearchDefinitionInterface;
use App\DaViEntity\Validator\ValidatorDefinitionInterface;
use App\Item\ItemInterface;
use App\Item\Property\PropertyConfiguration;
use App\Services\EntityAction\EntityActionConfigAttrInterface;
use App\Services\Version\VersionListInterface;
use Generator;
use Iterator;

class EntitySchema implements EntitySchemaInterface {

  private string $entityLabel;
  private string $entityType;
  private string $description;

  private string $baseTable;
  private array $columns = [];
  private string $database;

  private array $properties;

  private array $filters = [];
  private array $defaultFilters = [];
  private array $mandatoryFilters = [];
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

  private array $entityActions = [];

  /**
   * @var AdditionalDataProviderDefinitionInterface[]
   */
  private array $additionalDataProviderDefinitions = [];

  /**
   * @var ColumnBuilderDefinitionInterface[]
   */
  private array $columnBuildersDefinition = [];

  /**
   * @var CreatorDefinitionInterface[]
   */
  private array $creatorDefinitions = [];

  /**
   * @var DataProviderDefinitionInterface[]
   */
  private array $dataProviderDefinitions = [];

  /**
   * @var ListProviderDefinitionInterface[]
   */
  private array $listProviderDefinitions = [];

  /**
   * @var RefinerDefinitionInterface[]
   */
  private array $refinerDefinitions = [];

  /**
   * @var RepositoryDefinitionInterface[]
   */
  private array $repositoryDefinitions = [];

  /**
   * @var SearchDefinitionInterface[]
   */
  private array $searchDefinitions = [];

  /**
   * @var ValidatorDefinitionInterface[]
   */
  private array $validatorDefinition = [];

  public function __construct(
    private readonly string $entityClass,
  ) {
    $filterGroup = new FilterGroup(EntitySchemaInterface::WITHOUT_FILTER_GROUP);
    $this->addFilterGroup($filterGroup);
  }

  public function getEntityClass(): string {
    return $this->entityClass;
  }

  public function getDatabase(): string {
    return $this->database;
  }

  public function setDatabase(string $database): EntitySchema {
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

  public function addFilter(SqlFilterDefinitionInterface $filterDefinition): EntitySchemaInterface {
    $filterKey = $filterDefinition->getKey();
    $this->filters[$filterKey] = $filterDefinition;

    if($filterDefinition->hasGroupKey()) {
      $groupKey = $filterDefinition->getGroupKey();
    } elseif(!$filterDefinition->isGroup()) {
      $groupKey = EntitySchemaInterface::WITHOUT_FILTER_GROUP;
    } else {
      $groupKey = $filterDefinition->getProperty();
      if(!$this->hasFilterGroup($groupKey)) {
        $this->createFilterGroup($groupKey);
      }
    }

    $this->groupFilterMapping[$groupKey][] = $filterKey;

    return $this;
  }

  private function createFilterGroup(string $groupKey): void {
    $group = new FilterGroup($groupKey);
    $this->addFilterGroup($group);
  }

  public function getFilterDefinition(string $filterKey): SqlFilterDefinitionInterface | null {
    return $this->filters[$filterKey] ?? null;
  }

  public function iterateFilterDefinitions(): Generator {
    foreach ($this->filters as $key => $definition) {
      yield $key => $definition;
    }
  }

  public function iterateFilterGroups(): Generator {
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
    return $this->searchProperties ?? $this->getEntityLabelProperties();
  }

  public function setSearchProperties(array $searchProperties): EntitySchemaInterface {
    $this->searchProperties = $searchProperties;
    return $this;
  }

  public function addTableReference(TableReferenceAttrInterface $tableReferenceConfiguration): EntitySchemaInterface {
    if($tableReferenceConfiguration->isValid()) {
      $name = $tableReferenceConfiguration->getName();
      $this->tableReferences[$name] = $tableReferenceConfiguration;
    }
    return $this;
  }

  public function getTableReference(string $internalName): TableReferenceAttrInterface {
    return $this->tableReferences[$internalName] ?? TableReferenceAttr::createNullTableReference($internalName, $this->entityType . '_' . $internalName);
  }

  public function iterateTableReferences(): Generator {
    foreach($this->tableReferences as $internalName => $tableReferenceConfiguration) {
      yield $internalName => $tableReferenceConfiguration;
    }
  }

  public function addTableReferenceColumn(TableReferenceAttrInterface $tableReference, string $column, string $property): EntitySchemaInterface {
    $this->tableReferenceColumns[$tableReference->getName()][$property] = $column;
    return $this;
  }

  /**
   * @return string[]
   */
  public function getTableReferenceColumns(string $tableReferenceInternalName): array {
    return $this->tableReferenceColumns[$tableReferenceInternalName] ?? [];
  }

  public function addEntityAction(EntityActionConfigAttrInterface $actionConfiguration): EntitySchemaInterface {
    if($actionConfiguration->isValid()) {
      $this->entityActions[] = $actionConfiguration;
    }

    return $this;
  }

  public function iterateEntityActions(): Generator {
    foreach($this->entityActions as $actionConfiguration) {
      yield $actionConfiguration;
    }
  }

  public function addAdditionalDataProviderDefinition(AdditionalDataProviderDefinitionInterface $definition): EntitySchemaInterface {
    $this->additionalDataProviderDefinitions[] = $definition;
    return $this;
  }

  /**
   * @return Iterator<AdditionalDataProviderDefinitionInterface[]>
   */
  public function iterateAdditionalDataProviderDefinitions(string $version): Iterator {
    foreach ($this->additionalDataProviderDefinitions as $definition) {
      if(!$definition instanceof VersionListInterface || !$definition->hasVersion($version)) {
        continue;
      }
      yield $definition;
    }
  }

  public function addColumnsBuilderDefinition(ColumnBuilderDefinitionInterface $definition): EntitySchemaInterface {
    $this->columnBuildersDefinition[] = $definition;
    return $this;
  }

  public function getColumnsBuilderDefinition(string $version): ColumnBuilderDefinitionInterface | string {
    foreach ($this->columnBuildersDefinition as $definition) {
      if(!$definition instanceof VersionListInterface || !$definition->hasVersion($version)) {
        continue;
      }
      return $definition;
    }

    return '';
  }

  public function addCreatorDefinition(CreatorDefinitionInterface $definition): EntitySchemaInterface {
    $this->creatorDefinitions[] = $definition;
    return $this;
  }

  public function getCreatorDefinition(string $version): CreatorDefinitionInterface | string {
    foreach ($this->creatorDefinitions as $definition) {
      if(!$definition instanceof VersionListInterface || !$definition->hasVersion($version)) {
        continue;
      }
      return $definition;
    }

    return '';
  }

  public function addDataProviderDefinition(DataProviderDefinitionInterface $definition): EntitySchemaInterface {
    $this->dataProviderDefinitions[] = $definition;
    return $this;
  }

  public function getDataProviderDefinition(string $version): DataProviderDefinitionInterface | string {
    foreach ($this->dataProviderDefinitions as $definition) {
      if(!$definition instanceof VersionListInterface || !$definition->hasVersion($version)) {
        continue;
      }
      return $definition;
    }

    return '';
  }

  public function addListProviderDefinition(ListProviderDefinitionInterface $definition): EntitySchemaInterface {
    $this->listProviderDefinitions[] = $definition;
    return $this;
  }

  public function getListProviderDefinition(string $version): ListProviderDefinitionInterface | string {
    foreach ($this->listProviderDefinitions as $definition) {
      if(!$definition instanceof VersionListInterface || !$definition->hasVersion($version)) {
        continue;
      }
      return $definition;
    }

    return '';
  }

  public function addRefinerDefinition(RefinerDefinitionInterface $definition): EntitySchemaInterface {
    $this->refinerDefinitions[] = $definition;
    return $this;
  }

  public function getRefinerDefinition(string $version): RefinerDefinitionInterface | string {
    foreach ($this->refinerDefinitions as $definition) {
      if(!$definition instanceof VersionListInterface || !$definition->hasVersion($version)) {
        continue;
      }
      return $definition;
    }

    return '';
  }

  public function addRepositoryDefinition(RepositoryDefinitionInterface $definition): EntitySchemaInterface {
    $this->repositoryDefinitions[] = $definition;
    return $this;
  }

  public function getRepositoryDefinition(string $version): RepositoryDefinitionInterface | string {
    foreach ($this->repositoryDefinitions as $definition) {
      if(!$definition instanceof VersionListInterface || !$definition->hasVersion($version)) {
        continue;
      }
      return $definition;
    }

    return '';
  }

  public function addSearchDefinition(SearchDefinitionInterface $definition): EntitySchemaInterface {
    $this->searchDefinitions[] = $definition;
    return $this;
  }

  public function getSearchDefinition(string $version): SearchDefinitionInterface | string {
    foreach ($this->searchDefinitions as $definition) {
      if(!$definition instanceof VersionListInterface || !$definition->hasVersion($version)) {
        continue;
      }
      return $definition;
    }

    return '';
  }

  public function addValidatorDefinition(ValidatorDefinitionInterface $definition): EntitySchemaInterface {
    $this->validatorDefinition[] = $definition;
    return $this;
  }

  public function getValidatorDefinition(string $version): ValidatorDefinitionInterface | string {
    foreach ($this->validatorDefinition as $definition) {
      if(!$definition instanceof VersionListInterface || !$definition->hasVersion($version)) {
        continue;
      }
      return $definition;
    }

    return '';
  }

}