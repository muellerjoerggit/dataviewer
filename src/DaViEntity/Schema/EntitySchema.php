<?php

namespace App\DaViEntity\Schema;

use App\Database\AggregationHandler\Attribute\AggregationDefinitionInterface;
use App\Database\BaseQuery\BaseQueryDefinitionInterface;
use App\Database\SqlFilter\FilterContainer;
use App\Database\SqlFilter\FilterGroup;
use App\Database\SqlFilterHandler\Attribute\SqlFilterDefinitionInterface;
use App\Database\TableReferenceHandler\Attribute\TableReferenceDefinition;
use App\Database\TableReferenceHandler\Attribute\TableReferenceDefinitionInterface;
use App\EntityServices\AdditionalData\AdditionalDataProviderDefinitionInterface;
use App\EntityServices\AggregatedData\AggregatedDataProviderDefinitionInterface;
use App\EntityServices\ColumnBuilder\ColumnBuilderDefinitionInterface;
use App\EntityServices\Creator\CreatorDefinitionInterface;
use App\EntityServices\DataProvider\DataProviderDefinitionInterface;
use App\EntityServices\EntityLabel\LabelCrafterDefinitionInterface;
use App\EntityServices\ListProvider\ListProviderDefinitionInterface;
use App\EntityServices\OverviewBuilder\OverviewBuilderDefinitionInterface;
use App\EntityServices\Refiner\RefinerDefinitionInterface;
use App\EntityServices\Repository\RepositoryDefinitionInterface;
use App\EntityServices\SimpleSearch\SimpleSearchDefinitionInterface;
use App\EntityServices\Validator\ValidatorDefinitionInterface;
use App\EntityServices\ViewBuilder\ViewBuilderDefinitionInterface;
use App\Item\ItemInterface;
use App\Item\Property\PropertyConfiguration;
use App\Services\EntityAction\EntityActionDefinitionInterface;
use App\Services\Version\VersionListWrapperInterface;
use Generator;

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
   * @var SimpleSearchDefinitionInterface[]
   */
  private array $searchDefinitions = [];

  /**
   * @var ValidatorDefinitionInterface[]
   */
  private array $validatorDefinition = [];

  /**
   * @var BaseQueryDefinitionInterface[]
   */
  private array $baseQueryDefinitions = [];

  /**
   * @var ViewBuilderDefinitionInterface[]
   */
  private array $viewBuilderDefinitions = [];

  /**
   * @var OverviewBuilderDefinitionInterface[]
   */
  private array $overviewBuilderDefinitions = [];

  /**
   * @var AggregatedDataProviderDefinitionInterface[]
   */
  private array $aggregatedDataProviderDefinitions = [];

  /**
   * @var LabelCrafterDefinitionInterface[]
   */
  private array $labelCrafterDefinitions = [];

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

  public function addColumn(PropertyConfiguration $propertyConfiguration): EntitySchema {
    if($propertyConfiguration->hasColumn() && !$propertyConfiguration->hasTableReference()) {
      $this->columns[$propertyConfiguration->getItemName()] = $propertyConfiguration->getColumn();
    }
    return $this;
  }

  public function addProperty(PropertyConfiguration $property): EntitySchemaInterface {
    $name = $property->getItemName();
    $this->properties[$name] = $property;
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

  public function addAggregation(AggregationDefinitionInterface $aggregationDefinition): EntitySchemaInterface {
    $name = $aggregationDefinition->getName();
    $this->aggregations[$name] = $aggregationDefinition;
    return $this;
  }

  public function hasAggregations(): bool {
    return !empty($this->aggregations);
  }

  public function getAggregation(string $name): AggregationDefinitionInterface | null {
    if(isset($this->aggregations[$name])) {
      return $this->aggregations[$name];
    }

    return null;
  }

  /**
   * @return Generator<AggregationDefinitionInterface>
   */
  public function iterateAggregations(): Generator {
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

  public function addTableReference(TableReferenceDefinitionInterface $tableReferenceConfiguration): EntitySchemaInterface {
    if($tableReferenceConfiguration->isValid()) {
      $name = $tableReferenceConfiguration->getName();
      $this->tableReferences[$name] = $tableReferenceConfiguration;
    }
    return $this;
  }

  public function hasTableReference(string $internalName): bool {
    return isset($this->tableReferences[$internalName]);
  }

  public function getTableReference(string $internalName): TableReferenceDefinitionInterface {
    return $this->tableReferences[$internalName] ?? TableReferenceDefinition::createNullTableReference($internalName, $this->entityType . '_' . $internalName);
  }

  public function iterateTableReferences(): Generator {
    foreach($this->tableReferences as $internalName => $tableReferenceConfiguration) {
      yield $internalName => $tableReferenceConfiguration;
    }
  }

  public function addTableReferenceColumn(TableReferenceDefinitionInterface $tableReference, PropertyConfiguration $referencePropertyConfiguration, string $property): EntitySchemaInterface {
    $this->tableReferenceColumns[$tableReference->getName()][$property] = $referencePropertyConfiguration;
    return $this;
  }

  /**
   * @return string[]
   */
  public function getTableReferenceColumns(string $tableReferenceInternalName): array {
    return $this->tableReferenceColumns[$tableReferenceInternalName] ?? [];
  }

  public function addEntityAction(EntityActionDefinitionInterface $actionConfiguration): EntitySchemaInterface {
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
   * @return Generator<string>
   */
  public function iterateAdditionalDataProviderClasses(string $version): Generator {
    foreach ($this->additionalDataProviderDefinitions as $definition) {
      if(!$definition->hasVersion($version)) {
        continue;
      }
      yield $definition->getAdditionalDataProviderClass();
    }
  }

  public function addColumnsBuilderDefinition(ColumnBuilderDefinitionInterface $definition): EntitySchemaInterface {
    $this->columnBuildersDefinition[] = $definition;
    return $this;
  }

  public function getColumnsBuilderClass(string $version): string {
    foreach ($this->columnBuildersDefinition as $definition) {
      if(!$definition->hasVersion($version)) {
        continue;
      }
      return $definition->getColumnBuilderClass();
    }

    return '';
  }

  public function addCreatorDefinition(CreatorDefinitionInterface $definition): EntitySchemaInterface {
    $this->creatorDefinitions[] = $definition;
    return $this;
  }

  public function getCreatorClass(string $version): string {
    foreach ($this->creatorDefinitions as $definition) {
      if(!$definition->hasVersion($version)) {
        continue;
      }
      return $definition->getCreatorClass();
    }

    return '';
  }

  public function addDataProviderDefinition(DataProviderDefinitionInterface $definition): EntitySchemaInterface {
    $this->dataProviderDefinitions[] = $definition;
    return $this;
  }

  public function getDataProviderClass(string $version): DataProviderDefinitionInterface | string {
    foreach ($this->dataProviderDefinitions as $definition) {
      if(!$definition->hasVersion($version)) {
        continue;
      }
      return $definition->getDataProviderClass();
    }

    return '';
  }

  public function addListProviderDefinition(ListProviderDefinitionInterface $definition): EntitySchemaInterface {
    $this->listProviderDefinitions[] = $definition;
    return $this;
  }

  public function getListProviderClass(string $version): string {
    foreach ($this->listProviderDefinitions as $definition) {
      if(!$definition->hasVersion($version)) {
        continue;
      }
      return $definition->getListProviderClass();
    }

    return '';
  }

  public function addRefinerDefinition(RefinerDefinitionInterface $definition): EntitySchemaInterface {
    $this->refinerDefinitions[] = $definition;
    return $this;
  }

  public function getRefinerClass(string $version): string {
    foreach ($this->refinerDefinitions as $definition) {
      if(!$definition->hasVersion($version)) {
        continue;
      }
      return $definition->getRefinerClass();
    }

    return '';
  }

  public function addRepositoryDefinition(RepositoryDefinitionInterface $definition): EntitySchemaInterface {
    $this->repositoryDefinitions[] = $definition;
    return $this;
  }

  public function getRepositoryClass(string $version): string {
    foreach ($this->repositoryDefinitions as $definition) {
      if(!$definition->hasVersion($version)) {
        continue;
      }
      return $definition->getRepositoryClass();
    }

    return '';
  }

  public function addBaseQueryDefinition(BaseQueryDefinitionInterface $definition): EntitySchemaInterface {
    $this->baseQueryDefinitions[] = $definition;
    return $this;
  }

  public function getBaseQueryClass(string $version): string {
    foreach ($this->baseQueryDefinitions as $definition) {
      if(!$definition->hasVersion($version)) {
        continue;
      }
      return $definition->getBaseQueryClass();
    }

    return '';
  }

  public function addSimpleSearchDefinition(SimpleSearchDefinitionInterface $definition): EntitySchemaInterface {
    if($definition->isValid()) {
      $this->searchDefinitions[] = $definition;
    }
    return $this;
  }

  public function getSimpleSearchClass(string $version): string {
    foreach ($this->searchDefinitions as $definition) {
      if(
        !$definition instanceof VersionListWrapperInterface
        || !$definition->hasVersion($version)
        || !$definition instanceof SimpleSearchDefinitionInterface
      ) {
        continue;
      }
      return $definition->getSimpleSearchClass();
    }

    return '';
  }

  public function addValidatorDefinition(ValidatorDefinitionInterface $definition): EntitySchemaInterface {
    $this->validatorDefinition[] = $definition;
    return $this;
  }

  public function getValidatorClass(string $version): string {
    foreach ($this->validatorDefinition as $definition) {
      if(
        !$definition instanceof VersionListWrapperInterface
        || !$definition->hasVersion($version)
        || !$definition instanceof ValidatorDefinitionInterface
      ) {
        continue;
      }
      return $definition->getValidatorClass();
    }

    return '';
  }

  public function addViewBuilderDefinition(ViewBuilderDefinitionInterface $definition): EntitySchemaInterface {
    $this->viewBuilderDefinitions[] = $definition;
    return $this;
  }

  public function getViewBuilderClass(string $version): string {
    foreach ($this->viewBuilderDefinitions as $definition) {
      if(!$definition->hasVersion($version)) {
        continue;
      }
      return $definition->getViewBuilderClass();
    }

    return '';
  }

  public function addOverviewBuilderDefinition(OverviewBuilderDefinitionInterface $definition): EntitySchemaInterface {
    $this->overviewBuilderDefinitions[] = $definition;
    return $this;
  }

  public function getOverviewBuilderClass(string $version): string {
    foreach ($this->overviewBuilderDefinitions as $definition) {
      if(!$definition->hasVersion($version)) {
        continue;
      }
      return $definition->getOverviewBuilderClass();
    }

    return '';
  }

  public function addAggregatedDataProviderDefinition(AggregatedDataProviderDefinitionInterface $definition): EntitySchemaInterface {
    $this->aggregatedDataProviderDefinitions[] = $definition;
    return $this;
  }

  public function getAggregatedDataProviderClass(string $version): string {
    foreach ($this->aggregatedDataProviderDefinitions as $definition) {
      if(!$definition->hasVersion($version)) {
        continue;
      }
      return $definition->getAggregatedDataProviderClass();
    }

    return '';
  }

  public function addLabelCrafterDefinition(LabelCrafterDefinitionInterface $definition): EntitySchemaInterface {
    $this->labelCrafterDefinitions[] = $definition;
    return $this;
  }

  public function getLabelCrafterClass(string $version): string {
    foreach ($this->labelCrafterDefinitions as $definition) {
      if(!$definition->hasVersion($version)) {
        continue;
      }
      return $definition->getLabelCrafterClass();
    }

    return '';
  }

}