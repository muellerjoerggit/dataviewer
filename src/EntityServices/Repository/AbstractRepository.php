<?php

namespace App\EntityServices\Repository;

use App\Database\SqlFilter\FilterContainer;
use App\Database\SqlFilter\SqlFilter;
use App\Database\SqlFilterHandler\Attribute\SqlFilterDefinition;
use App\Database\SqlFilterHandler\EntityKeyFilterHandler;
use App\DataCollections\EntityList;
use App\DaViEntity\EntityInterface;
use App\DaViEntity\EntityKey;
use App\DaViEntity\MainRepository;
use App\DaViEntity\Schema\EntityTypesRegister;
use App\EntityServices\AdditionalData\AdditionalDataProviderLocator;
use App\EntityServices\AvailabilityVerdict\AvailabilityVerdictLocator;
use App\EntityServices\Creator\CreatorLocator;
use App\EntityServices\DataProvider\DataProviderLocator;
use App\EntityServices\ListProvider\ListProviderLocator;
use App\EntityServices\Refiner\RefinerLocator;
use App\EntityServices\Validator\ValidatorLocator;

abstract class AbstractRepository implements RepositoryInterface {

  public function __construct(
    protected readonly EntityTypesRegister $entityTypesRegister,
    protected readonly MainRepository $mainRepository,
    protected readonly DataProviderLocator $entityDataProviderLocator,
    protected readonly CreatorLocator $entityCreatorLocator,
    protected readonly AdditionalDataProviderLocator $additionalDataProviderLocator,
    protected readonly RefinerLocator $entityRefinerLocator,
    protected readonly ListProviderLocator $entityListProviderLocator,
    protected readonly ValidatorLocator $validatorLocator,
    protected readonly string $entityClass
  ) {}

  public function loadEntityData(FilterContainer $filterContainer, array $options = []): array {
    $entityDataProvider = $this->entityDataProviderLocator->getEntityDataProvider($this->entityClass, $filterContainer->getClient());
    return $entityDataProvider->fetchEntityData($this->entityClass, $filterContainer, $options);
  }

  public function loadMultipleEntities(FilterContainer $filterContainer, array $options = []): array {
    $data = $this->loadEntityData($filterContainer, $options);
    $ret = [];

    $creator = $this->entityCreatorLocator->getEntityCreator($this->entityClass, $filterContainer->getClient());

    foreach ($data as $row) {
      $ret[] = $creator->createEntity($this->entityClass, $filterContainer->getClient(), $row);
    }

    return $ret;
  }

  public function loadEntityByEntityKey(EntityKey $entityKey): EntityInterface {
    $definition = new SqlFilterDefinition(EntityKeyFilterHandler::class, '', 'EntityKeyFilterHandler');
    $filter = new SqlFilter($definition, [$entityKey], 'entityKeyFilter');
    $filterContainer = new FilterContainer($entityKey->getClient(), [$filter]);

    $data = $this->loadEntityData($filterContainer);

    $creator = $this->entityCreatorLocator->getEntityCreator($this->entityClass, $entityKey->getClient());

    if(empty($data)){
      $entity = $creator->createMissingEntity($entityKey);
    } else {
      $entity = $creator->createEntity($this->entityClass, $entityKey->getClient(), reset($data));
    }

    $this->mainRepository->addEntity($entity);
    $this->processAdditionalData($entity);
    $this->refineEntity($entity);

    return $entity;
  }

  public function getEntityList(FilterContainer $filterContainer): EntityList {
    return $this->entityListProviderLocator->getEntityListProvider($this->entityClass, $filterContainer->getClient())->getEntityList($this->entityClass, $filterContainer);
  }

  protected function refineEntity(EntityInterface $entity): void {
    $refiner = $this->entityRefinerLocator->getEntityRefiner($entity::class, $entity->getClient());
    $refiner->refineEntity($entity);
    $refiner->setAvailability($entity);
    $this->validateEntity($entity);
  }

  protected function processAdditionalData(EntityInterface $entity): void {
    $dataProviders = $this->additionalDataProviderLocator->getAdditionalDataProviders($entity::class, $entity->getClient());
    foreach ($dataProviders as $dataProvider) {
      $dataProvider->loadData($entity);
    }
  }

  protected function validateEntity(EntityInterface $entity): void {
    $validator = $this->validatorLocator->getValidator($entity->getSchema(), $entity->getClient());
    $validator->validateEntity($entity);
  }

}