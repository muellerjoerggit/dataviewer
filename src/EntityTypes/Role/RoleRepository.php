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
use App\DaViEntity\Validator\ValidatorLocator;

class RoleRepository extends AbstractRepository {

  public function __construct(
    EntityTypesRegister $entityTypesRegister,
    MainRepository $mainRepository,
    DataProviderLocator $entityDataProviderLocator,
    CreatorLocator $entityCreatorLocator,
    AdditionalDataProviderLocator $additionalDataProviderLocator,
    RefinerLocator $entityRefinerLocator,
    ListProviderLocator $entityListProviderLocator,
    ValidatorLocator $validatorLocator,
  ) {
    parent::__construct(
      $entityTypesRegister,
      $mainRepository,
      $entityDataProviderLocator,
      $entityCreatorLocator,
      $additionalDataProviderLocator,
      $entityRefinerLocator,
      $entityListProviderLocator,
      $validatorLocator,
      RoleEntity::class
    );
  }

}