<?php

namespace App\EntityTypes\Role;

use App\DaViEntity\AdditionalData\AdditionalDataProviderLocator;
use App\DaViEntity\Creator\CreatorLocator;
use App\DaViEntity\DataProvider\DataProviderLocator;
use App\DaViEntity\ListProvider\ListProviderLocator;
use App\DaViEntity\Refiner\RefinerLocator;
use App\DaViEntity\Repository\AbstractRepository;
use App\DaViEntity\MainRepository;
use App\DaViEntity\Schema\EntityTypesRegister;

class RoleRepository extends AbstractRepository {

  public function __construct(
    EntityTypesRegister $entityTypesRegister,
    MainRepository $mainRepository,
    DataProviderLocator $entityDataProviderLocator,
    CreatorLocator $entityCreatorLocator,
    AdditionalDataProviderLocator $additionalDataProviderLocator,
    RefinerLocator $entityRefinerLocator,
    ListProviderLocator $entityListProviderLocator,
  ) {
    parent::__construct(
      $entityTypesRegister,
      $mainRepository,
      $entityDataProviderLocator,
      $entityCreatorLocator,
      $additionalDataProviderLocator,
      $entityRefinerLocator,
      $entityListProviderLocator,
      RoleEntity::class
    );
  }

}