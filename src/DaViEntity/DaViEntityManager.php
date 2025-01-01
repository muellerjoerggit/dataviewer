<?php

namespace App\DaViEntity;

use App\Database\Aggregation\AggregationConfiguration;
use App\Database\DaViQueryBuilder;
use App\Database\SqlFilter\FilterContainer;
use App\DataCollections\EntityList;
use App\DataCollections\TableData;
use App\DaViEntity\EntityTypes\NullEntity\NullEntity;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use App\DaViEntity\Schema\EntityTypesRegister;
use App\Item\ItemHandler_EntityReference\EntityReferenceItemHandlerLocator;
use App\Item\ItemInterface;

class DaViEntityManager {

  public function __construct(
    private readonly EntityTypesRegister $entityTypesRegister,
    private readonly EntityTypeSchemaRegister $schemaRegister,
    private readonly EntityReferenceItemHandlerLocator $referenceItemHandlerLocator,
    private readonly MainRepository $mainRepository,
  ) {}

  public function loadEntityData(string $entityType, FilterContainer $filterContainer, array $options = []): array {
    $controller = $this->getEntityController($entityType);
    return $controller->loadEntityData($filterContainer, $options);
  }

  public function getEntityController($input): EntityControllerInterface {
    return $this->entityTypesRegister->resolveEntityController($input);
  }

  public function loadMultipleEntities(string $entityType, FilterContainer $filterContainer, array $options = []): array {
    $controller = $this->getEntityController($entityType);
    return $controller->loadMultipleEntities($filterContainer, $options);
  }

  public function loadAggregatedEntityData($input, string $client, string|AggregationConfiguration $aggregation, FilterContainer $filterContainer = NULL, array $options = []): array|TableData {
    $controller = $this->getEntityController($input);
    return $controller->loadAggregatedData($client, $aggregation, $filterContainer, $options);
  }

  public function getEntityLabel(mixed $entityKey): string {
    $entity = $this->evaluateEntity($entityKey);
    $controller = $this->getEntityController($entity);
    return $controller->getEntityLabel($entity);
  }

  public function evaluateEntity(mixed $entity): EntityInterface {
    if (is_string($entity)) {
      $entity = EntityKey::createFromString($entity);
    }

    if ($entity instanceof EntityKey) {
      $entity = $this->getEntity($entity);
    }

    if ($entity instanceof EntityInterface) {
      return $entity;
    } else {
      return $this->createNullEntity();
    }
  }

  public function getEntity(EntityKey $entityKey): EntityInterface {
    if($this->mainRepository->entityExists($entityKey)) {
      return $this->mainRepository->getEntity($entityKey);
    }

    return $this->loadEntityByEntityKey($entityKey);
  }

  public function getEntityListFromSearchString(string $client, string $entityType, string $searchString): array {
    $controller = $this->getEntityController($entityType);
    return $controller->getEntityListFromSearchString($client, $searchString);
  }

  public function loadEntityByEntityKey(EntityKey $entityKey): EntityInterface {
    $controller = $this->getEntityController($entityKey);
    $entity = $controller->loadEntityByEntityKey($entityKey);
    if(!$this->mainRepository->entityExists($entityKey)) {
      $this->mainRepository->addEntity($entity);
    }
    return $entity;
  }

  public function createNullEntity(): EntityInterface {
    $schema = $this->schemaRegister->getEntityTypeSchema(NullEntity::ENTITY_TYPE);
    return new NullEntity($schema, 'unknown');
  }

  public function preRenderEntity(mixed $entityKey): array {
    $entity = $this->evaluateEntity($entityKey);
    $controller = $this->getEntityController($entity);
    return $controller->preRenderEntity($entity);
  }

  public function getEntityOverview(mixed $entityKey, array $options = []): array {
    $entity = $this->evaluateEntity($entityKey);
    $controller = $this->getEntityController($entity);
    return $controller->getEntityOverview($entity, $options);
  }

  public function getExtendedEntityOverview(mixed $entity, $options = []): array {
    $entity = $this->evaluateEntity($entity);
    $controller = $this->getEntityController($entity);
    return $controller->getExtendedEntityOverview($entity, $options);
  }

  public function getItemsFromPath(string $path, $baseEntity): array {
    $currentEntity = $this->evaluateEntity($baseEntity);
    $ret = [];
    $separatorPos = strpos($path, '.');
    if ($separatorPos) {
      $pathSection = substr($path, 0, $separatorPos);
      $path = substr($path, $separatorPos + 1);
    } else {
      $pathSection = $path;
      $path = '';
    }

    $item = $currentEntity->getPropertyItem($pathSection);
    if ($item->getConfiguration()
        ->hasEntityReferenceHandler() && !empty($path)) {
      $itemConfiguration = $item->getConfiguration();
      $handler = $this->referenceItemHandlerLocator->getEntityReferenceHandlerFromItem($itemConfiguration);

      foreach ($handler->iterateEntityKeys($currentEntity, $pathSection) as $entityKey) {
        if (!$entityKey) {
          continue;
        }
        $ret = array_merge($this->getItemsFromPath($path, $entityKey), $ret);
      }
    } elseif ($item instanceof ItemInterface) {
      $ret[] = $item;
    }

    return $ret;
  }

  public function getEntityList(string $entityType, FilterContainer $filterContainer): EntityList {
    $controller = $this->getEntityController($entityType);
    return $controller->getEntityList($filterContainer);
  }

  public function getBaseQueryBuilder(string $entityType, string $client, array $options = []): DaViQueryBuilder {
    $controller = $this->getEntityController($entityType);
    return $controller->buildQueryFromSchema($client, $options);
  }

}