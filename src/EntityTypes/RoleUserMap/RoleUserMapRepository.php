<?php

namespace App\EntityTypes\RoleUserMap;

use App\DaViEntity\AdditionalData\AdditionalDataProviderLocator;
use App\DaViEntity\Creator\EntityCreatorLocator;
use App\DaViEntity\DataProvider\EntityDataProviderLocator;
use App\DaViEntity\ListProvider\EntityListProviderLocator;
use App\DaViEntity\Refiner\EntityRefinerLocator;
use App\DaViEntity\Repository\AbstractEntityRepository;
use App\DaViEntity\MainRepository;
use App\DaViEntity\Schema\EntityTypesRegister;

class RoleUserMapRepository extends AbstractEntityRepository {

  public function __construct(
    EntityTypesRegister $entityTypesRegister,
    MainRepository $mainRepository,
    EntityDataProviderLocator $entityDataProviderLocator,
    EntityCreatorLocator $entityCreatorLocator,
    AdditionalDataProviderLocator $additionalDataProviderLocator,
    EntityRefinerLocator $entityRefinerLocator,
    EntityListProviderLocator $entityListProviderLocator,
  ) {
    parent::__construct(
      $entityTypesRegister,
      $mainRepository,
      $entityDataProviderLocator,
      $entityCreatorLocator,
      $additionalDataProviderLocator,
      $entityRefinerLocator,
      $entityListProviderLocator,
      RoleUserMapEntity::class
    );
  }

}