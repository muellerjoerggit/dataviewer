<?php

namespace App\DaViEntity\EntityRepository;

use App\Database\SqlFilter\FilterContainer;
use App\Database\SqlFilter\SqlFilter;
use App\Database\SqlFilter\SqlFilterDefinition;
use App\DataCollections\EntityList;
use App\DaViEntity\AdditionalData\AdditionalDataProviderLocator;
use App\DaViEntity\EntityCreator\EntityCreatorLocator;
use App\DaViEntity\EntityDataProvider\EntityDataProviderLocator;
use App\DaViEntity\EntityInterface;
use App\DaViEntity\EntityKey;
use App\DaViEntity\EntityListProvider\EntityListProviderLocator;
use App\DaViEntity\EntityRefiner\EntityRefinerLocator;
use App\DaViEntity\MainRepository;
use App\DaViEntity\Schema\EntityTypesRegister;

abstract class AbstractEntityRepository implements EntityRepositoryInterface {

  public function __construct(
    protected readonly EntityTypesRegister $entityTypesRegister,
    protected readonly MainRepository $mainRepository,
    protected readonly EntityDataProviderLocator $entityDataProviderLocator,
    protected readonly EntityCreatorLocator $entityCreatorLocator,
    protected readonly AdditionalDataProviderLocator $additionalDataProviderLocator,
    protected readonly EntityRefinerLocator $entityRefinerLocator,
    protected readonly EntityListProviderLocator $entityListProviderLocator,
    protected readonly string $entityClass
  ) {}

  public function loadEntityData(FilterContainer $filterContainer, array $options = []): array {
    $entityDataProvider = $this->entityDataProviderLocator->getEntityDataProvider($this->entityClass);
    return $entityDataProvider->fetchEntityData($this->entityClass, $filterContainer, $options);
  }

  public function loadMultipleEntities(FilterContainer $filterContainer, array $options = []): array {
    $data = $this->loadEntityData($filterContainer, $options);
    $ret = [];

    $creator = $this->entityCreatorLocator->getEntityCreator($this->entityClass);

    foreach ($data as $row) {
      $ret[] = $creator->createEntity($this->entityClass, $filterContainer->getClient(), $row);
    }

    return $ret;
  }

  public function loadEntityByEntityKey(EntityKey $entityKey): EntityInterface {
    $definition = new SqlFilterDefinition('entityKeyFilter', 'EntityKeyFilterHandler');
    $filter = new SqlFilter($definition, [$entityKey], 'entityKeyFilter');
    $filterContainer = new FilterContainer($entityKey->getClient(), [$filter]);

    $data = $this->loadEntityData($filterContainer);

    $creator = $this->entityCreatorLocator->getEntityCreator($this->entityClass);

    if(empty($data)){
      $entity = $creator->createMissingEntity($entityKey);
    } else {
      $entity = $creator->createEntity($this->entityClass, $entityKey->getClient(), reset($data));
    }

    $this->mainRepository->addEntity($entity);
    $this->refineEntity($entity);

    return $entity;
  }

  public function getEntityList(FilterContainer $filterContainer): EntityList {
    return $this->entityListProviderLocator->getEntityListProvider($this->entityClass)->getEntityList($this->entityClass, $filterContainer);
  }

  protected function refineEntity(EntityInterface $entity): void {
    $this->processAdditionalData($entity);
    $this->entityRefinerLocator->getEntityRefiner($entity)->refineEntity($entity);
  }

  protected function processAdditionalData(EntityInterface $entity): void {
    $dataProviders = $this->additionalDataProviderLocator->getAdditionalDataProviders($entity::class);
    foreach ($dataProviders as $dataProvider) {
      $dataProvider->loadData($entity);
    }
  }

}