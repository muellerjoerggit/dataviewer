<?php

namespace App\DaViEntity;

use App\Database\Aggregation\AggregationConfiguration;
use App\Database\DaViQueryBuilder;
use App\Database\SqlFilter\FilterContainer;
use App\Database\SqlFilter\SqlFilter;
use App\Database\SqlFilter\SqlFilterDefinition;
use App\DataCollections\EntityList;
use App\DataCollections\TableData;
use App\DaViEntity\AdditionalData\AdditionalDataProviderLocator;
use App\DaViEntity\Schema\EntitySchema;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use App\DaViEntity\Schema\EntityTypesReader;

abstract class AbstractEntityController implements EntityControllerInterface{

  protected EntitySchema $schema;

  public function __construct(
    protected readonly EntityDataMapperInterface $dataMapper,
    protected readonly EntityCreatorInterface $creator,
    protected readonly EntitySearchInterface $searchEntity,
    protected readonly EntityTypeSchemaRegister $entityTypeSchemaRegister,
    protected readonly EntityViewBuilderInterface $entityViewBuilder,
    protected readonly EntityRefinerInterface $entityRefiner,
    protected readonly EntityValidatorInterface $entityValidator,
    protected readonly AdditionalDataProviderLocator $additionalDataProviderLocator,
  ) {
    $this->schema = $this->getEntitySchema();
  }

  public function getEntityListFromSearchString(string $client, string $searchString): array {
    $uniqueProperties = $this->schema->getUniqueProperties();
    $uniqueProperty = reset($uniqueProperties);
    $uniqueProperty = reset($uniqueProperty);
    $uniqueColumn = $this->schema->getColumn($uniqueProperty);

    if(str_contains($uniqueProperty,'+')) {
      return [];
    }

    $searchResult = $this->searchEntity->getEntityListFromSearchString($client, $this->schema, $searchString, $uniqueColumn);

    return $searchResult;
  }

  protected function getEntitySchema(): EntitySchema{
    $reflection = new \ReflectionClass($this);
    $entityType = EntityTypesReader::getEntityTypeFromReflection($reflection);
    return $this->entityTypeSchemaRegister->getEntityTypeSchema($entityType);
  }

  public function loadEntityData(FilterContainer $filterContainer, array $options = []): array {
    return $this->dataMapper->fetchEntityData($this->schema, $filterContainer, $options);
  }

  public function loadMultipleEntities(FilterContainer $filterContainer, array $options = []): array {
    $data = $this->dataMapper->fetchEntityData($this->schema, $filterContainer, $options);
    $ret = [];
    foreach ($data as $row) {
      $ret[] = $this->creator->createEntity($this->schema, $filterContainer->getClient(), $row);
    }
    return $ret;
  }

  public function loadEntityByEntityKey(EntityKey $entityKey): EntityInterface {
    $definition = new SqlFilterDefinition('entityKeyFilter', 'EntityKeyFilterHandler');
    $filter = new SqlFilter($definition, [$entityKey], 'entityKeyFilter');
    $filterContainer = new FilterContainer($entityKey->getClient(), [$filter]);

    $data = $this->loadEntityData($filterContainer);

    if(empty($data)){
      return $this->creator->createMissingEntity($entityKey, $this->schema);
    } else {
      $entity = $this->creator->createEntity($this->schema, $entityKey->getClient(), reset($data));
      $this->refineEntity($entity);
      return $entity;
    }
  }

  public function loadAggregatedData(string $client, AggregationConfiguration $aggregation, FilterContainer $filterContainer = null, array $columnsBlacklist = []): array | TableData {
    return $this->dataMapper->fetchAggregatedData($client, $this->schema, $aggregation, $filterContainer, $columnsBlacklist);
  }

  public function preRenderEntity(EntityInterface $entity): array {
    return $this->entityViewBuilder->preRenderEntity($entity);
  }

  public function getExtendedEntityOverview(EntityInterface $entity, $options): array {
    return $this->entityViewBuilder->buildExtendedEntityOverview($entity, $options);
  }

  public function getEntityOverview(EntityInterface $entity, array $options = []): array {
    return $this->entityViewBuilder->buildEntityOverview($entity, $options);
  }

  public function getEntityList(FilterContainer $filterContainer): EntityList {
    return $this->dataMapper->getEntityList($this->schema, $filterContainer);
  }

  public function getEntityLabel(EntityInterface $entity): string {
    $entityLabelProperties = $this->schema->getEntityLabelProperties();
    $uniqueProperties = $this->schema->getUniqueProperties();
    $uniqueProperties = reset($uniqueProperties);
    $label = '';
    foreach ($entityLabelProperties as $property) {
      $value = $entity->getPropertyValueAsString($property);
      $label = empty($label) ? $value : $label . ' ' . $value;
    }

    $unique = '';
    foreach ($uniqueProperties as $property) {
      $value = $entity->getPropertyValueAsString($property);
      $unique = empty($unique) ? $value : $unique . ' ' . $value;
    }

    if(!empty($unique)) {
      $label = $label . ' (' . $unique . ')';
    }

    return $label;
  }

  public function validateEntity(EntityInterface $entity): void {
    $this->entityValidator->validateEntity($entity);
  }

  public function refineEntity(EntityInterface $entity): EntityInterface {
    $this->processAdditionalData($entity);
    return $this->entityRefiner->refineEntity($entity);
  }

  protected function processAdditionalData(EntityInterface $entity): void {
    $dataProviders = $this->additionalDataProviderLocator->getAdditionalDataProviders($entity::class);
    foreach ($dataProviders as $dataProvider) {
      $dataProvider->loadData($entity);
    }
  }

  public function buildQueryFromSchema(string $client, array $options = []): DaViQueryBuilder {
    return $this->dataMapper->buildQueryFromSchema($this->schema, $client, $options);
  }

}