<?php

namespace App\DaViEntity\Schema;

use App\Database\BaseQuery\BaseQuery;
use App\Database\SqlFilterHandler\Attribute\SqlFilterDefinitionInterface;
use App\Database\TableReferenceHandler\Attribute\TableReferenceAttrInterface;
use App\DaViEntity\AdditionalData\AdditionalDataProvider;
use App\DaViEntity\ColumnBuilder\ColumnBuilderDefinition;
use App\DaViEntity\Creator\CreatorDefinition;
use App\DaViEntity\DataProvider\DataProviderDefinition;
use App\DaViEntity\ListProvider\ListProviderDefinition;
use App\DaViEntity\Search\SearchDefinition;
use App\DaViEntity\Refiner\RefinerDefinition;
use App\DaViEntity\Repository\RepositoryDefinition;
use App\DaViEntity\Schema\Attribute\DatabaseAttr;
use App\DaViEntity\Schema\Attribute\EntityOverviewDefinitionInterface;
use App\DaViEntity\Schema\Attribute\EntityTypeAttr;
use App\DaViEntity\Schema\Attribute\ExtEntityOverviewDefinitionInterface;
use App\DaViEntity\Schema\Attribute\LabelDefinitionInterface;
use App\Item\Property\Attribute\SearchPropertyDefinition;
use App\Item\Property\Attribute\UniquePropertyDefinition;
use App\Item\Property\PropertyAttributesContainer;
use App\Services\EntityAction\EntityActionConfigAttrInterface;
use Generator;

class SchemaAttributesContainer {

  private EntityTypeAttr $entityTypeAttr;
  private DatabaseAttr $databaseAttr;

  /**
   * @var RepositoryDefinition[]
   */
  private array $repositoryAttributes = [];

  /**
   * @var BaseQuery[]
   */
  private array $baseQueryAttributes = [];

  /**
   * @var SearchDefinition[]
   */
  private array $entityListSearchAttributes = [];

  /**
   * @var DataProviderDefinition[]
   */
  private array $entityDataProviderAttributes = [];

  /**
   * @var CreatorDefinition[]
   */
  private array $entityCreatorAttributes = [];

  /**
   * @var RefinerDefinition[]
   */
  private array $entityRefinerAttributes = [];

  /**
   * @var ColumnBuilderDefinition[]
   */
  private array $entityColumnBuilderAttributes = [];

  /**
   * @var ListProviderDefinition[]
   */
  private array $entityListProviderAttributes = [];

  /**
   * @var AdditionalDataProvider[]
   */
  private array $additionalDataProviderAttributes = [];

  /**
   * @var TableReferenceAttrInterface[]
   */
  private array $tableReferenceAttributes = [];

  /**
   * @var EntityActionConfigAttrInterface[]
   */
  private array $entityActionAttributes = [];

  /**
   * @var SqlFilterDefinitionInterface[]
   */
  private array $sqlFilterAttributes = [];

  /**
   * @var PropertyAttributesContainer[]
   */
  private array $properties = [];

  /**
   * @var LabelDefinitionInterface[]
   */
  private array $labelDefinitionAttributes = [];

  /**
   * @var EntityOverviewDefinitionInterface[]
   */
  private array $entityOverviewDefinitionDefinitions = [];

  /**
   * @var SearchPropertyDefinition[]
   */
  private array $searchPropertyDefinitions = [];

  /**
   * @var UniquePropertyDefinition[]
   */
  private array $uniquePropertyDefinitions = [];

  /**
   * @var ExtEntityOverviewDefinitionInterface[]
   */
  private array $extendedEntityOverviewDefinitions = [];

  public function getEntityTypeAttr(): EntityTypeAttr | null {
    return $this->entityTypeAttr ?? null;
  }

  public function setEntityTypeAttr(EntityTypeAttr $entityTypeAttr): SchemaAttributesContainer {
    $this->entityTypeAttr = $entityTypeAttr;
    return $this;
  }

  public function getDatabaseAttr(): DatabaseAttr | null {
    return $this->databaseAttr ?? null;
  }

  public function setDatabaseAttr(DatabaseAttr $databaseAttr): SchemaAttributesContainer {
    $this->databaseAttr = $databaseAttr;
    return $this;
  }

  public function addRepositoryAttribute(RepositoryDefinition $repositoryAttr): SchemaAttributesContainer {
    $this->repositoryAttributes[] = $repositoryAttr;
    return $this;
  }

  public function addBaseQueryAttribute(BaseQuery $baseQuery): SchemaAttributesContainer {
    $this->baseQueryAttributes[] = $baseQuery;
    return $this;
  }

  public function addEntityListSearchAttribute(SearchDefinition $listSearch): SchemaAttributesContainer {
    $this->entityListSearchAttributes[] = $listSearch;
    return $this;
  }

  public function addDataProviderAttribute(DataProviderDefinition $dataProvider): SchemaAttributesContainer {
    $this->entityDataProviderAttributes[] = $dataProvider;
    return $this;
  }

  public function addCreatorAttribute(CreatorDefinition $creator): SchemaAttributesContainer {
    $this->entityCreatorAttributes[] = $creator;
    return $this;
  }

  public function addRefinerAttribute(RefinerDefinition $refiner): SchemaAttributesContainer {
    $this->entityRefinerAttributes[] = $refiner;
    return $this;
  }

  public function addColumnBuilderAttribute(ColumnBuilderDefinition $columnBuilder): SchemaAttributesContainer {
    $this->entityColumnBuilderAttributes[] = $columnBuilder;
    return $this;
  }

  public function addListProviderAttribute(ListProviderDefinition $listProvider): SchemaAttributesContainer {
    $this->entityListProviderAttributes[] = $listProvider;
    return $this;
  }

  public function addAdditionalDataProviderAttribute(AdditionalDataProvider $additionalDataProvider): SchemaAttributesContainer {
    $this->additionalDataProviderAttributes[] = $additionalDataProvider;
    return $this;
  }

  public function addTableReferenceAttribute(TableReferenceAttrInterface $tableReferenceAttr): SchemaAttributesContainer {
    $this->tableReferenceAttributes[] = $tableReferenceAttr;
    return $this;
  }

  /**
   * @return Generator<TableReferenceAttrInterface>
   */
  public function iterateTableReferenceAttributes(): Generator {
    foreach ($this->tableReferenceAttributes as $tableReferenceAttr) {
      yield $tableReferenceAttr;
    }
  }

  public function addEntityActionConfigAttribute(EntityActionConfigAttrInterface $entityActionAttribute): SchemaAttributesContainer {
    $this->entityActionAttributes[] = $entityActionAttribute;
    return $this;
  }

  /**
   * @return Generator<EntityActionConfigAttrInterface>
   */
  public function iterateEntityActionConfigAttributes(): Generator {
    foreach ($this->entityActionAttributes as $entityActionAttribute) {
      yield $entityActionAttribute;
    }
  }

  public function hasEntityActions(): bool {
    return !empty($this->entityActionAttributes);
  }

  public function addPropertyContainer(PropertyAttributesContainer $container, string $key): SchemaAttributesContainer {
    $this->properties[$key] = $container;
    return $this;
  }

  /**
   * @return Generator<PropertyAttributesContainer>
   */
  public function iteratePropertyContainer(): Generator {
    foreach ($this->properties as $key => $container) {
      yield $key => $container;
    }
  }

  public function addSqlFilterDefinitionsAttribute(SqlFilterDefinitionInterface $filterDefinition): SchemaAttributesContainer {
    $this->sqlFilterAttributes[] = $filterDefinition;
    return $this;
  }

  /**
   * @return Generator<SqlFilterDefinitionInterface>
   */
  public function iterateSqlFilterDefinitionAttributes(): Generator {
    foreach ($this->sqlFilterAttributes as $filterConfig) {
      yield $filterConfig;
    }
  }

  public function hasSqlFilterDefinitions(): bool {
    return !empty($this->sqlFilterAttributes);
  }

  public function addLabelDefinition(LabelDefinitionInterface $labelDefinition): SchemaAttributesContainer {
    $this->labelDefinitionAttributes[] = $labelDefinition;
    return $this;
  }

  /**
   * @return Generator<LabelDefinitionInterface>
   */
  public function iterateLabelDefinitions(): Generator {
    foreach ($this->labelDefinitionAttributes as $labelDefinition) {
      yield $labelDefinition;
    }
  }

  public function addEntityOverviewDefinition(EntityOverviewDefinitionInterface $entityOverviewDefinition): SchemaAttributesContainer {
    $this->entityOverviewDefinitionDefinitions[] = $entityOverviewDefinition;
    return $this;
  }

  /**
   * @return Generator<EntityOverviewDefinitionInterface>
   */
  public function iterateEntityOverviewDefinitions(): Generator {
    foreach ($this->entityOverviewDefinitionDefinitions as $entityOverviewDefinition) {
      yield $entityOverviewDefinition;
    }
  }

  public function addSearchPropertyDefinition(SearchPropertyDefinition $searchPropertyDefinition): SchemaAttributesContainer {
    $this->searchPropertyDefinitions[] = $searchPropertyDefinition;
    return $this;
  }

  /**
   * @return Generator<SearchPropertyDefinition>
   */
  public function iterateSearchPropertyDefinitions(): Generator {
    foreach ($this->searchPropertyDefinitions as $searchPropertyDefinition) {
      yield $searchPropertyDefinition;
    }
  }

  public function hasUniquePropertyDefinitions(): bool {
    return !empty($this->uniquePropertyDefinitions);
  }

  public function addUniquePropertyDefinition(UniquePropertyDefinition $uniquePropertyDefinition): SchemaAttributesContainer {
    $this->uniquePropertyDefinitions[] = $uniquePropertyDefinition;
    return $this;
  }

  /**
   * @return Generator<EntityOverviewDefinitionInterface>
   */
  public function iterateUniquePropertyDefinitions(): Generator {
    foreach ($this->uniquePropertyDefinitions as $uniquePropertyDefinition) {
      yield $uniquePropertyDefinition;
    }
  }

  public function addExtendedEntityOverviewDefinition(ExtEntityOverviewDefinitionInterface $extendedEntityOverviewDefinition): SchemaAttributesContainer {
    $this->extendedEntityOverviewDefinitions[] = $extendedEntityOverviewDefinition;
    return $this;
  }

  /**
   * @return Generator<ExtEntityOverviewDefinitionInterface>
   */
  public function iterateExtendedEntityOverviewDefinitions(): Generator {
    foreach ($this->extendedEntityOverviewDefinitions as $extendedEntityOverviewDefinition) {
      yield $extendedEntityOverviewDefinition;
    }
  }

  public function isValid(): bool {
    return !empty($this->uniquePropertyDefinitions);
  }

}