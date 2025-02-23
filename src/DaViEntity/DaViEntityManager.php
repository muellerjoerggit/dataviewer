<?php

namespace App\DaViEntity;

use App\Database\AggregationHandler\Attribute\AggregationDefinitionInterface;
use App\Database\SqlFilter\FilterContainer;
use App\DataCollections\EntityList;
use App\DataCollections\TableData;
use App\DaViEntity\OverviewBuilder\OverviewBuilderLocator;
use App\DaViEntity\SimpleSearch\SimpleSearchLocator;
use App\DaViEntity\Repository\RepositoryInterface;
use App\DaViEntity\Repository\RepositoryLocator;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use App\DaViEntity\Schema\EntityTypesRegister;
use App\DaViEntity\ViewBuilder\ViewBuilderLocator;
use App\EntityServices\AggregatedData\AggregatedDataProviderLocator;
use App\EntityServices\EntityLabel\LabelCrafterLocator;
use App\EntityTypes\NullEntity\NullEntity;
use App\Item\ItemInterface;

class DaViEntityManager {

  public function __construct(
    private readonly EntityTypesRegister $entityTypesRegister,
    private readonly EntityTypeSchemaRegister $schemaRegister,
    private readonly MainRepository $mainRepository,
    private readonly SimpleSearchLocator $entityListSearchLocator,
    private readonly RepositoryLocator $entityRepositoryLocator,
    private readonly ViewBuilderLocator $viewBuilderLocator,
    private readonly OverviewBuilderLocator $overviewBuilderLocator,
    private readonly AggregatedDataProviderLocator $aggregatedDataProviderLocator,
    private readonly LabelCrafterLocator $labelCrafterLocator,
  ) {}

  public function loadEntityData(string $entityType, FilterContainer $filterContainer, array $options = []): array {
    return $this->getEntityRepositoryFromEntityType($entityType, $filterContainer->getClient())->loadEntityData($filterContainer, $options);
  }

  public function loadMultipleEntities(string $entityType, FilterContainer $filterContainer, array $options = []): array {
    return $this->getEntityRepositoryFromEntityType($entityType, $filterContainer->getClient())->loadMultipleEntities($filterContainer, $options);
  }

  public function loadAggregatedData(string $entityClass, string $client, AggregationDefinitionInterface $aggregation, FilterContainer $filterContainer = NULL, array $options = []): TableData {
    $provider = $this->aggregatedDataProviderLocator->getAggregatedDataProvider($entityClass, $client);
    return $provider->fetchAggregatedData($entityClass, $client, $aggregation, $filterContainer, $options);
  }

  public function getEntityLabel(mixed $entityKey): string {
    $entity = $this->evaluateEntity($entityKey);
    $crafter = $this->labelCrafterLocator->getEntityLabelCrafter($entity::class, $entity->getClient());
    return $crafter->getEntityLabel($entity);
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
    $entityClass = $this->entityTypesRegister->getEntityClassByEntityType($entityType);
    $entityListSearch = $this->entityListSearchLocator->getSimpleSearch($entityClass, $client);
    return $entityListSearch->getEntityListFromSearchString($entityClass, $client, $searchString);
  }

  public function loadEntityByEntityKey(EntityKey $entityKey): EntityInterface {
    return $this->getEntityRepositoryFromEntityType($entityKey->getEntityType(), $entityKey->getClient())->loadEntityByEntityKey($entityKey);
  }

  public function createNullEntity(): EntityInterface {
    $schema = $this->schemaRegister->getEntityTypeSchema(NullEntity::ENTITY_TYPE);
    return new NullEntity($schema, 'unknown');
  }

  public function preRenderEntity(mixed $entityKey): array {
    $entity = $this->evaluateEntity($entityKey);
    $viewBuilder = $this->viewBuilderLocator->getViewBuilder($entity::class, $entity->getClient());
    return $viewBuilder->preRenderEntity($entity);
  }

  public function getEntityOverview(mixed $entityKey, array $options = []): array {
    $entity = $this->evaluateEntity($entityKey);
    $overviewBuilder = $this->overviewBuilderLocator->getOverviewBuilder($entity->getSchema(), $entity->getClient());
    return $overviewBuilder->buildEntityOverview($entity, $options);
  }

  public function getExtendedEntityOverview(mixed $entity, $options = []): array {
    $entity = $this->evaluateEntity($entity);
    $overviewBuilder = $this->overviewBuilderLocator->getOverviewBuilder($entity->getSchema(), $entity->getClient());
    return $overviewBuilder->buildExtendedEntityOverview($entity, $options);
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
    if ($item->getConfiguration()->hasEntityReferenceHandler() && !empty($path)) {
      foreach ($item->iterateEntityKeys() as $entityKey) {
        $ret = array_merge($this->getItemsFromPath($path, $entityKey), $ret);
      }
    } elseif ($item instanceof ItemInterface) {
      $ret[] = $item;
    }

    return $ret;
  }

  public function getEntityList(string $entityClass, FilterContainer $filterContainer): EntityList {
    return $this->getEntityRepository($entityClass, $filterContainer->getClient())->getEntityList($filterContainer);
  }

  public function getEntityListFromEntityType(string $entityType, FilterContainer $filterContainer): EntityList {
    return $this->getEntityRepositoryFromEntityType($entityType, $filterContainer->getClient())->getEntityList($filterContainer);
  }

  /**
   * @return EntityInterface[]
   */
  public function getEntitiesFromEntityPath(array $entityPath, EntityInterface $baseEntity): array {
    $currentEntities = [$baseEntity];

    foreach ($entityPath as $propertyKey) {
      $entities = [];
      foreach ($currentEntities as $currentEntity) {
        if(!$currentEntity->hasPropertyItem($propertyKey)) {
          return [];
        }
        $propertyItem = $currentEntity->getPropertyItem($propertyKey);

        if(!$propertyItem->hasEntityKeys()) {
          return [];
        }

        foreach ($propertyItem->iterateEntityKeys() as $entityKey) {
          $entities[] = $this->getEntity($entityKey);
        }
      }

      $currentEntities = $entities;
    }

    return $currentEntities;
  }

  protected function getEntityRepository(string | EntityInterface $entityClass, string $client): RepositoryInterface {
    return $this->entityRepositoryLocator->getEntityRepository($entityClass, $client);
  }

  protected function getEntityRepositoryFromEntityType(string $entityType, string $client): RepositoryInterface {
    $entityClass = $this->entityTypesRegister->getEntityClassByEntityType($entityType);
    return $this->getEntityRepository($entityClass, $client);
  }

}