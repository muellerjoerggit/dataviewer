<?php

namespace App\DaViEntity;

use App\Database\Aggregation\AggregationConfiguration;
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
  ) {}

  public function getEntity(EntityKey $entityKey): EntityInterface {
    return $this->loadEntityByEntityKey($entityKey);
  }

  public function getEntityController($input): EntityControllerInterface {
    return $this->entityTypesRegister->resolveEntityController($input);
  }

  public function loadEntityData(string $entityType, FilterContainer $filterContainer, array $options = []): array {
    $controller = $this->getEntityController($entityType);
    return $controller->loadEntityData($filterContainer, $options);
  }

  public function loadMultipleEntities(string $entityType, FilterContainer $filterContainer, array $options = []): array {
    $controller = $this->getEntityController($entityType);
    return $controller->loadMultipleEntities($filterContainer, $options);
  }

  public function loadEntityByEntityKey(EntityKey $entityKey): EntityInterface {
    $controller = $this->getEntityController($entityKey);
    return $controller->loadEntityByEntityKey($entityKey);
  }

  public function loadAggregatedEntityData($input, string $client, string | AggregationConfiguration $aggregation, FilterContainer $filterContainer = null, array $columnsBlacklist = []): array | TableData {
    $controller = $this->getEntityController($input);
    return $controller->loadAggregatedData($client, $aggregation, $filterContainer, $columnsBlacklist);
  }

  public function getEntityLabel(mixed $entityKey): string {
    $entity = $this->evaluateEntity($entityKey);
    $controller = $this->getEntityController($entity);
    return $controller->getEntityLabel($entity);
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
    if($separatorPos) {
      $pathSection = substr($path, 0, $separatorPos);
      $path = substr($path, $separatorPos + 1);
    } else {
      $pathSection = $path;
      $path = '';
    }

    $item = $currentEntity->getPropertyItem($pathSection);
    if($item->getConfiguration()->hasEntityReferenceHandler() && !empty($path)) {
      $itemConfiguration = $item->getConfiguration();
      $handler = $this->referenceItemHandlerLocator->getEntityReferenceHandlerFromItem($itemConfiguration);

      foreach ($handler->iterateEntityKeys($currentEntity, $pathSection) as $entityKey) {
        if(!$entityKey) {
          continue;
        }
        $ret = array_merge($this->getItemsFromPath($path, $entityKey), $ret);
      }
    } elseif ($item instanceof ItemInterface) {
      $ret[] = $item;
    }

    return $ret;
  }

  public function evaluateEntity(mixed $entity): EntityInterface {

    if(is_string($entity)) {
      $entity = EntityKey::createFromString($entity);
    }

    if ($entity instanceof EntityKey) {
      $entity = $this->getEntity($entity);
    }

    if($entity instanceof EntityInterface) {
      return $entity;
    } else {
      return $this->createNullEntity();
    }
  }

  public function createNullEntity(): EntityInterface {
    $schema = $this->schemaRegister->getEntityTypeSchema(NullEntity::ENTITY_TYPE);
    return new NullEntity($schema, 'unknown');
  }

  public function getEntityList(string $entityType, FilterContainer $filterContainer): EntityList {
    $controller = $this->getEntityController($entityType);
    return $controller->getEntityList($filterContainer);
  }

}