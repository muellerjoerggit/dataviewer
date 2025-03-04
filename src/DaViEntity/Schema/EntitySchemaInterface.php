<?php

namespace App\DaViEntity\Schema;

use App\Database\AggregationHandler\Attribute\AggregationDefinitionInterface;
use App\Database\SqlFilter\FilterContainer;
use App\Database\SqlFilter\FilterGroup;
use App\Database\SqlFilterHandler\Attribute\SqlFilterDefinitionInterface;
use App\Database\TableReference\TableReferenceConfiguration;
use App\Item\Property\PropertyConfiguration;
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

  public function iterateProperties(): Generator;

  public function getDefaultFiltersAsContainer(string $client): FilterContainer;

  public function getMandatoryFiltersAsContainer(string $client): FilterContainer;

  public function hasMandatoryFilters(): bool;

  public function getFilterDefinition(string $filterKey): SqlFilterDefinitionInterface | null;

  public function hasFilter(string $filterKey): bool;

  public function addFilter(SqlFilterDefinitionInterface $filterDefinition): EntitySchemaInterface;

  /**
   * @return \Generator<SqlFilterDefinitionInterface>
   */
  public function iterateFilterDefinitions(): Generator;

  /**
   * @return \Generator<FilterGroup>
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
   * @return \Generator<AggregationDefinitionInterface>
   */
  public function iterateAggregations(): Generator;

  public function isSingleColumnPrimaryKeyInteger(): bool;

  /**
   * @return \Generator<TableReferenceConfiguration>
   */
  public function iterateTableReferences(): \Generator;

  public function setSearchProperties(array $searchProperties): EntitySchemaInterface;

  public function getSearchProperties(): array;

}