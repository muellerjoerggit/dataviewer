<?php

namespace App\EntityTypes\User;

use App\DaViEntity\MainRepository;
use App\DaViEntity\Schema\EntityTypesRegister;
use App\EntityServices\AdditionalData\AdditionalDataProviderLocator;
use App\EntityServices\Creator\CreatorLocator;
use App\EntityServices\DataProvider\DataProviderLocator;
use App\EntityServices\ListProvider\ListProviderLocator;
use App\EntityServices\Refiner\RefinerLocator;
use App\EntityServices\Repository\AbstractRepository;
use App\EntityServices\Validator\ValidatorLocator;

class UserRepository extends AbstractRepository {

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
      UserEntity::class
    );
  }

}