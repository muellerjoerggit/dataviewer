<?php

namespace App\DaViEntity;

use App\Database\Aggregation\AggregationConfiguration;
use App\Database\SqlFilter\FilterContainer;
use App\DataCollections\TableData;
use App\DaViEntity\Schema\EntitySchema;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use App\DaViEntity\Schema\EntityTypesReader;

abstract class AbstractEntityController implements EntityControllerInterface{

  protected EntitySchema $schema;

  public function __construct(
    protected readonly EntityDataMapperInterface $dataMapper,
    protected readonly EntityTypeSchemaRegister $entityTypeSchemaRegister,
    protected readonly EntityViewBuilderInterface $entityViewBuilder,
  ) {
    $this->schema = $this->getEntitySchema();
  }

  protected function getEntitySchema(): EntitySchema{
    $reflection = new \ReflectionClass($this);
    $entityType = EntityTypesReader::getEntityTypeFromReflection($reflection);
    return $this->entityTypeSchemaRegister->getEntityTypeSchema($entityType);
  }

  public function loadAggregatedData(string $client, AggregationConfiguration $aggregation, FilterContainer $filterContainer = null, array $options = []): array | TableData {
    return $this->dataMapper->fetchAggregatedData($client, $this->schema, $aggregation, $filterContainer, $options);
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

}