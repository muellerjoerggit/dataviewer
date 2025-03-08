<?php

namespace App\DaViEntity\Schema;

use App\Database\AggregationHandler\Attribute\AggregationDefinitionInterface;
use App\Database\BaseQuery\BaseQueryDefinitionInterface;
use App\Database\SqlFilter\FilterContainer;
use App\Database\SqlFilter\FilterGroup;
use App\Database\SqlFilterHandler\Attribute\SqlFilterDefinitionInterface;
use App\Database\TableReference\TableReferenceConfiguration;
use App\Database\TableReferenceHandler\Attribute\TableReferenceDefinitionInterface;
use App\EntityServices\AdditionalData\AdditionalDataProviderDefinitionInterface;
use App\EntityServices\AggregatedData\AggregatedDataProviderDefinitionInterface;
use App\EntityServices\AvailabilityVerdict\AvailabilityVerdictDefinitionInterface;
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
use App\Item\Property\PropertyConfiguration;
use App\Services\EntityAction\EntityActionDefinitionInterface;
use Generator;

interface EntitySchemaInterface {

  public const string WITHOUT_FILTER_GROUP = 'withoutGroup';

  public function setEntityType(string $entityType): EntitySchemaInterface;

  public function getEntityType(): string;

  public function setEntityLabel(string $entityLabel): EntitySchemaInterface;

  public function getEntityLabel(): string;

  public function setBaseTable(string $baseTable): EntitySchemaInterface;

  public function getBaseTable(): string;

  public function getColumns(): array;

  public function getColumn(string $property): string;

  public function addProperty(PropertyConfiguration $property): EntitySchemaInterface;

  public function getProperty(string $property): PropertyConfiguration;

  public function hasProperty(string $property): bool;

  /**
   * @return Generator<PropertyConfiguration>
   */
  public function iterateProperties(): Generator;

  public function getDefaultFiltersAsContainer(string $client): FilterContainer;

  public function getMandatoryFiltersAsContainer(string $client): FilterContainer;

  public function hasMandatoryFilters(): bool;

  public function getFilterDefinition(string $filterKey): SqlFilterDefinitionInterface | null;

  public function hasFilter(string $filterKey): bool;

  public function addFilter(SqlFilterDefinitionInterface $filterDefinition): EntitySchemaInterface;

  /**
   * @return Generator<SqlFilterDefinitionInterface>
   */
  public function iterateFilterDefinitions(): Generator;

  /**
   * @return Generator<FilterGroup>
   */
  public function iterateFilterGroups(): Generator;

  public function getUniqueProperties(): array;

  public function getFirstUniqueProperties(): array;

  public function setUniqueProperties(array $uniqueIdentifiers): EntitySchemaInterface;

  public function getEntityLabelProperties(): array;

  public function setEntityLabelProperties(array $entityLabelProperties): EntitySchemaInterface;

  public function getEntityOverviewProperties(): array;

  public function setEntityOverviewProperties(array $entityOverview): EntitySchemaInterface;

  public function getExtendedEntityOverviewProperties(): array;

  public function setExtendedEntityOverviewProperties(array $extendedEntityOverview): EntitySchemaInterface;

  public function addAggregation(AggregationDefinitionInterface $aggregationDefinition): EntitySchemaInterface;

  public function hasAggregations(): bool;

  public function getAggregation(string $name): AggregationDefinitionInterface | null;

  /**
   * @return Generator<AggregationDefinitionInterface>
   */
  public function iterateAggregations(): Generator;

  public function isSingleColumnPrimaryKeyInteger(): bool;

  /**
   * @return Generator<TableReferenceConfiguration>
   */
  public function iterateTableReferences(): Generator;

  /**
   * @param string[] $searchProperties
   */
  public function setSearchProperties(array $searchProperties): EntitySchemaInterface;

  /**
   * @return string[]
   */
  public function getSearchProperties(): array;

  public function getEntityClass(): string;

  public function getDatabase(): string;

  public function setDatabase(string $database): EntitySchema;

  public function getDescription(): string;

  public function setDescription(string $description): EntitySchema;

  public function addColumn(PropertyConfiguration $propertyConfiguration): EntitySchema;

  public function addFilterGroup(FilterGroup $filterGroup): EntitySchemaInterface;

  public function hasFilterGroup(string $groupKey): bool;

  public function getGroupFilterMappings(): array;

  public function addTableReference(TableReferenceDefinitionInterface $tableReferenceConfiguration): EntitySchemaInterface;

  public function hasTableReference(string $internalName): bool;

  public function getTableReference(string $internalName): TableReferenceDefinitionInterface;

  public function addTableReferenceColumn(TableReferenceDefinitionInterface $tableReference, PropertyConfiguration $referencePropertyConfiguration, string $property): EntitySchemaInterface;

  /**
   * @return string[]
   */
  public function getTableReferenceColumns(string $tableReferenceInternalName): array;

  public function addEntityAction(EntityActionDefinitionInterface $actionConfiguration): EntitySchemaInterface;

  /**
   * @return Generator<EntityActionDefinitionInterface>
   */
  public function iterateEntityActions(): Generator;

  public function addAdditionalDataProviderDefinition(AdditionalDataProviderDefinitionInterface $definition): EntitySchemaInterface;

  /**
   * @return Generator<string>
   */
  public function iterateAdditionalDataProviderClasses(string $version): Generator;

  public function addColumnsBuilderDefinition(ColumnBuilderDefinitionInterface $definition): EntitySchemaInterface;

  public function getColumnsBuilderClass(string $version): string;

  public function addCreatorDefinition(CreatorDefinitionInterface $definition): EntitySchemaInterface;

  public function getCreatorClass(string $version): string;

  public function addDataProviderDefinition(DataProviderDefinitionInterface $definition): EntitySchemaInterface;

  public function getDataProviderClass(string $version): DataProviderDefinitionInterface | string;

  public function addListProviderDefinition(ListProviderDefinitionInterface $definition): EntitySchemaInterface;

  public function getListProviderClass(string $version): string;

  public function addRefinerDefinition(RefinerDefinitionInterface $definition): EntitySchemaInterface;

  public function getRefinerClass(string $version): string;

  public function addRepositoryDefinition(RepositoryDefinitionInterface $definition): EntitySchemaInterface;

  public function getRepositoryClass(string $version): string;

  public function addBaseQueryDefinition(BaseQueryDefinitionInterface $definition): EntitySchemaInterface;

  public function getBaseQueryClass(string $version): string;

  public function addSimpleSearchDefinition(SimpleSearchDefinitionInterface $definition): EntitySchemaInterface;

  public function getSimpleSearchClass(string $version): string;

  public function addValidatorDefinition(ValidatorDefinitionInterface $definition): EntitySchemaInterface;

  public function getValidatorClass(string $version): string;

  public function addViewBuilderDefinition(ViewBuilderDefinitionInterface $definition): EntitySchemaInterface;

  public function getViewBuilderClass(string $version): string;

  public function addOverviewBuilderDefinition(OverviewBuilderDefinitionInterface $definition): EntitySchemaInterface;

  public function getOverviewBuilderClass(string $version): string;

  public function addAggregatedDataProviderDefinition(AggregatedDataProviderDefinitionInterface $definition): EntitySchemaInterface;

  public function getAggregatedDataProviderClass(string $version): string;

  public function addAvailabilityVerdictDefinition(AvailabilityVerdictDefinitionInterface $definition): EntitySchemaInterface;

  public function hasAvailabilityVerdictService(): bool;

  public function getAvailabilityVerdictServiceClass(string $version): string;

  public function addLabelCrafterDefinition(LabelCrafterDefinitionInterface $definition): EntitySchemaInterface;

  public function getLabelCrafterClass(string $version): string;


}