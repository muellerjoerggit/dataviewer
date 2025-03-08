<?php

namespace App\DaViEntity\Schema;

use App\Database\AggregationHandler\Attribute\AggregationDefinitionInterface;
use App\Database\BaseQuery\BaseQueryDefinitionInterface;
use App\Database\SqlFilterHandler\Attribute\SqlFilterDefinitionInterface;
use App\Database\TableReferenceHandler\Attribute\TableReferenceDefinitionInterface;
use App\DaViEntity\Schema\Attribute\DatabaseDefinition;
use App\DaViEntity\Schema\Attribute\EntityOverviewDefinitionInterface;
use App\DaViEntity\Schema\Attribute\EntityTypeDefinition;
use App\DaViEntity\Schema\Attribute\ExtEntityOverviewDefinitionInterface;
use App\DaViEntity\Schema\Attribute\LabelDefinitionInterface;
use App\EntityServices\AdditionalData\AdditionalDataProviderDefinitionInterface;
use App\EntityServices\AdditionalData\AdditionalDataProviderInterface;
use App\EntityServices\AggregatedData\AggregatedDataProviderDefinitionInterface;
use App\EntityServices\AvailabilityVerdict\AvailabilityVerdictDefinitionInterface;
use App\EntityServices\ColumnBuilder\ColumnBuilderDefinitionInterface;
use App\EntityServices\Creator\CreatorDefinitionInterface;
use App\EntityServices\DataProvider\DataProviderDefinitionInterface;
use App\EntityServices\EntityLabel\LabelCrafterDefinitionInterface;
use App\EntityServices\ListProvider\ListProviderDefinitionInterface;
use App\EntityServices\OverviewBuilder\OverviewBuilderDefinitionInterface;
use App\EntityServices\Refiner\RefinerDefinitionInterface;
use App\EntityServices\Repository\RepositoryDefinition;
use App\EntityServices\Repository\RepositoryDefinitionInterface;
use App\EntityServices\SimpleSearch\SimpleSearchDefinitionInterface;
use App\EntityServices\Validator\ValidatorDefinitionInterface;
use App\EntityServices\ViewBuilder\ViewBuilderDefinitionInterface;
use App\Item\Property\Attribute\SearchPropertyDefinition;
use App\Item\Property\Attribute\UniquePropertyDefinition;
use App\Item\Property\PropertyAttributesContainer;
use App\Services\EntityAction\EntityActionDefinitionInterface;
use Generator;

class SchemaDefinitionsContainer {

  private EntityTypeDefinition $entityTypeAttr;
  private DatabaseDefinition $databaseDefinition;

  /**
   * @var RepositoryDefinitionInterface[]
   */
  private array $repositoryDefinitions = [];

  /**
   * @var BaseQueryDefinitionInterface[]
   */
  private array $baseQueryDefinitions = [];

  /**
   * @var SimpleSearchDefinitionInterface[]
   */
  private array $simpleSearchDefinitions = [];

  /**
   * @var DataProviderDefinitionInterface[]
   */
  private array $dataProviderDefinitions = [];

  /**
   * @var CreatorDefinitionInterface[]
   */
  private array $creatorDefinitions = [];

  /**
   * @var RefinerDefinitionInterface[]
   */
  private array $refinerDefinitions = [];

  /**
   * @var ColumnBuilderDefinitionInterface[]
   */
  private array $columnBuilderDefinitions = [];

  /**
   * @var ListProviderDefinitionInterface[]
   */
  private array $listProviderDefinitions = [];

  /**
   * @var AdditionalDataProviderInterface[]
   */
  private array $additionalDataProviderDefinitions = [];

  /**
   * @var TableReferenceDefinitionInterface[]
   */
  private array $tableReferenceAttributes = [];

  /**
   * @var EntityActionDefinitionInterface[]
   */
  private array $entityActionDefinitions = [];

  /**
   * @var SqlFilterDefinitionInterface[]
   */
  private array $sqlFilterAttributes = [];

  /**
   * @var AggregationDefinitionInterface[]
   */
  private array $aggregationDefinitions = [];

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

  /**
   * @var ValidatorDefinitionInterface[]
   */
  private array $validatorDefinitions = [];

  /**
   * @var AvailabilityVerdictDefinitionInterface[]
   */
  private array $availabilityVerdictDefinitions = [];

  public function getEntityTypeAttr(): EntityTypeDefinition | null {
    return $this->entityTypeAttr ?? null;
  }

  public function setEntityTypeAttr(EntityTypeDefinition $entityTypeAttr): SchemaDefinitionsContainer {
    $this->entityTypeAttr = $entityTypeAttr;
    return $this;
  }

  public function getDatabaseDefinition(): DatabaseDefinition | null {
    return $this->databaseDefinition ?? null;
  }

  public function setDatabaseDefinition(DatabaseDefinition $databaseAttr): SchemaDefinitionsContainer {
    $this->databaseDefinition = $databaseAttr;
    return $this;
  }

  public function addRepositoryDefinition(RepositoryDefinitionInterface $repositoryAttr): SchemaDefinitionsContainer {
    $this->repositoryDefinitions[] = $repositoryAttr;
    return $this;
  }

  /**
   * @return Generator<RepositoryDefinition>
   */
  public function iterateRepositoryDefinitions(): Generator {
    foreach($this->repositoryDefinitions as $repositoryDefinition) {
      yield $repositoryDefinition;
    }
  }

  public function addBaseQueryDefinition(BaseQueryDefinitionInterface $baseQuery): SchemaDefinitionsContainer {
    $this->baseQueryDefinitions[] = $baseQuery;
    return $this;
  }

  /**
   * @return Generator<BaseQueryDefinitionInterface>
   */
  public function iterateBaseQueryDefinitions(): Generator {
    foreach($this->baseQueryDefinitions as $definition) {
      yield $definition;
    }
  }

  public function addSimpleSearchDefinition(SimpleSearchDefinitionInterface $simpleSearch): SchemaDefinitionsContainer {
    $this->simpleSearchDefinitions[] = $simpleSearch;
    return $this;
  }

  /**
   * @return Generator<SimpleSearchDefinitionInterface>
   */
  public function iterateSimpleSearchDefinitions(): Generator {
    foreach($this->simpleSearchDefinitions as $definition) {
      yield $definition;
    }
  }

  public function addDataProviderDefinition(DataProviderDefinitionInterface $dataProvider): SchemaDefinitionsContainer {
    $this->dataProviderDefinitions[] = $dataProvider;
    return $this;
  }

  /**
   * @return Generator<DataProviderDefinitionInterface>
   */
  public function iterateDataProviderDefinitions(): Generator {
    foreach($this->dataProviderDefinitions as $definition) {
      yield $definition;
    }
  }

  public function addCreatorDefinition(CreatorDefinitionInterface $creator): SchemaDefinitionsContainer {
    $this->creatorDefinitions[] = $creator;
    return $this;
  }

  /**
   * @return Generator<CreatorDefinitionInterface>
   */
  public function iterateCreatorDefinitions(): Generator {
    foreach($this->creatorDefinitions as $definition) {
      yield $definition;
    }
  }

  public function addRefinerDefinition(RefinerDefinitionInterface $refiner): SchemaDefinitionsContainer {
    $this->refinerDefinitions[] = $refiner;
    return $this;
  }

  /**
   * @return Generator<RefinerDefinitionInterface>
   */
  public function iterateRefinerDefinitions(): Generator {
    foreach($this->refinerDefinitions as $definition) {
      yield $definition;
    }
  }

  public function addColumnBuilderDefinition(ColumnBuilderDefinitionInterface $columnBuilder): SchemaDefinitionsContainer {
    $this->columnBuilderDefinitions[] = $columnBuilder;
    return $this;
  }

  /**
   * @return Generator<ColumnBuilderDefinitionInterface>
   */
  public function iterateColumnBuilderDefinitions(): Generator {
    foreach($this->columnBuilderDefinitions as $definition) {
      yield $definition;
    }
  }

  public function addListProviderDefinition(ListProviderDefinitionInterface $listProvider): SchemaDefinitionsContainer {
    $this->listProviderDefinitions[] = $listProvider;
    return $this;
  }

  /**
   * @return Generator<ListProviderDefinitionInterface>
   */
  public function iterateListProviderDefinitions(): Generator {
    foreach($this->listProviderDefinitions as $definition) {
      yield $definition;
    }
  }

  public function addAdditionalDataProviderDefinition(AdditionalDataProviderDefinitionInterface $definition): SchemaDefinitionsContainer {
    $this->additionalDataProviderDefinitions[] = $definition;
    return $this;
  }

  /**
   * @return Generator<AdditionalDataProviderDefinitionInterface>
   */
  public function iterateAdditionalDataProviderDefinitions(): Generator {
    foreach($this->additionalDataProviderDefinitions as $definition) {
      yield $definition;
    }
  }

  public function addTableReferenceAttribute(TableReferenceDefinitionInterface $tableReferenceAttr): SchemaDefinitionsContainer {
    $this->tableReferenceAttributes[] = $tableReferenceAttr;
    return $this;
  }

  /**
   * @return Generator<TableReferenceDefinitionInterface>
   */
  public function iterateTableReferenceAttributes(): Generator {
    foreach ($this->tableReferenceAttributes as $tableReferenceAttr) {
      yield $tableReferenceAttr;
    }
  }

  public function addEntityActionConfigAttribute(EntityActionDefinitionInterface $entityActionAttribute): SchemaDefinitionsContainer {
    $this->entityActionDefinitions[] = $entityActionAttribute;
    return $this;
  }

  /**
   * @return Generator<EntityActionDefinitionInterface>
   */
  public function iterateEntityActionConfigAttributes(): Generator {
    foreach ($this->entityActionDefinitions as $entityActionAttribute) {
      yield $entityActionAttribute;
    }
  }

  public function hasEntityActions(): bool {
    return !empty($this->entityActionDefinitions);
  }

  public function addPropertyContainer(PropertyAttributesContainer $container, string $key): SchemaDefinitionsContainer {
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

  public function addSqlFilterDefinitionsAttribute(SqlFilterDefinitionInterface $filterDefinition): SchemaDefinitionsContainer {
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

  public function addAggregationDefinitionAttribute(AggregationDefinitionInterface $aggregationDefinition): SchemaDefinitionsContainer {
    $this->aggregationDefinitions[] = $aggregationDefinition;
    return $this;
  }

  /**
   * @return Generator<AggregationDefinitionInterface>
   */
  public function iterateAggregationDefinitionAttributes(): Generator {
    foreach ($this->aggregationDefinitions as $aggregationDefinition) {
      yield $aggregationDefinition;
    }
  }

  public function hasAggregationDefinitions(): bool {
    return !empty($this->aggregationDefinitions);
  }

  public function addLabelDefinition(LabelDefinitionInterface $labelDefinition): SchemaDefinitionsContainer {
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

  public function addEntityOverviewDefinition(EntityOverviewDefinitionInterface $entityOverviewDefinition): SchemaDefinitionsContainer {
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

  public function addSearchPropertyDefinition(SearchPropertyDefinition $searchPropertyDefinition): SchemaDefinitionsContainer {
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

  public function addUniquePropertyDefinition(UniquePropertyDefinition $uniquePropertyDefinition): SchemaDefinitionsContainer {
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

  public function addExtendedEntityOverviewDefinition(ExtEntityOverviewDefinitionInterface $extendedEntityOverviewDefinition): SchemaDefinitionsContainer {
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

  public function addViewBuilderDefinition(ViewBuilderDefinitionInterface $definition): SchemaDefinitionsContainer {
    $this->viewBuilderDefinitions[] = $definition;
    return $this;
  }

  /**
   * @return Generator<ViewBuilderDefinitionInterface>
   */
  public function iterateViewBuilderDefinitions(): Generator {
    foreach ($this->viewBuilderDefinitions as $definition) {
      yield $definition;
    }
  }

  public function addOverviewBuilderDefinition(OverviewBuilderDefinitionInterface $definition): SchemaDefinitionsContainer {
    $this->overviewBuilderDefinitions[] = $definition;
    return $this;
  }

  /**
   * @return Generator<OverviewBuilderDefinitionInterface>
   */
  public function iterateOverviewBuilderDefinitions(): Generator {
    foreach ($this->overviewBuilderDefinitions as $definition) {
      yield $definition;
    }
  }

  public function addAggregatedDataProviderDefinition(AggregatedDataProviderDefinitionInterface $definition): SchemaDefinitionsContainer {
    $this->aggregatedDataProviderDefinitions[] = $definition;
    return $this;
  }

  /**
   * @return Generator<AggregatedDataProviderDefinitionInterface>
   */
  public function iterateAggregatedDataProviderDefinitions(): Generator {
    foreach ($this->aggregatedDataProviderDefinitions as $definition) {
      yield $definition;
    }
  }

  public function addLabelCrafterDefinition(LabelCrafterDefinitionInterface $definition): SchemaDefinitionsContainer {
    $this->labelCrafterDefinitions[] = $definition;
    return $this;
  }

  /**
   * @return Generator<LabelCrafterDefinitionInterface>
   */
  public function iterateLabelCrafterDefinitions(): Generator {
    foreach ($this->labelCrafterDefinitions as $definition) {
      yield $definition;
    }
  }


  public function addValidatorDefinition(ValidatorDefinitionInterface $definition): SchemaDefinitionsContainer {
    $this->validatorDefinitions[] = $definition;
    return $this;
  }

  /**
   * @return Generator<ValidatorDefinitionInterface>
   */
  public function iterateValidatorDefinitions(): Generator {
    foreach ($this->validatorDefinitions as $definition) {
      yield $definition;
    }
  }

  public function addAvailabilityVerdictDefinition(AvailabilityVerdictDefinitionInterface $definition): SchemaDefinitionsContainer {
    $this->availabilityVerdictDefinitions[] = $definition;
    return $this;
  }

  /**
   * @return Generator<AvailabilityVerdictDefinitionInterface>
   */
  public function iterateAvailabilityVerdictDefinitions(): Generator {
    foreach ($this->availabilityVerdictDefinitions as $definition) {
      yield $definition;
    }
  }

  public function isValid(): bool {
    return !empty($this->uniquePropertyDefinitions);
  }

}