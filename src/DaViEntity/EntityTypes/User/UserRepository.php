<?php

namespace App\DaViEntity\EntityTypes\User;

use App\DaViEntity\AdditionalData\AdditionalDataProviderLocator;
use App\DaViEntity\EntityCreator\EntityCreatorLocator;
use App\DaViEntity\EntityDataProvider\EntityDataProviderLocator;
use App\DaViEntity\EntityListProvider\EntityListProviderLocator;
use App\DaViEntity\EntityRefiner\EntityRefinerLocator;
use App\DaViEntity\EntityRepository\AbstractEntityRepository;
use App\DaViEntity\EntityTypes\RoleUserMap\RoleUserMapEntity;
use App\DaViEntity\MainRepository;
use App\DaViEntity\Schema\EntityTypesRegister;

class UserRepository extends AbstractEntityRepository {

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
      UserEntity::class
    );
  }

}