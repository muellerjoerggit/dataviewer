<?php

namespace App\DaViEntity\EntityTypes\User;

use App\Database\CommonBaseQuery;
use App\DaViEntity\AbstractEntity;
use App\DaViEntity\AdditionalData\AdditionalDataProvider;
use App\DaViEntity\AdditionalData\AdditionalDataProviderFromTableReferences;
use App\DaViEntity\Attribute\BaseQuery;
use App\DaViEntity\Attribute\EntityType;
use App\DaViEntity\Traits\EntityPropertyTrait;

#[EntityType(name: 'User')]
#[BaseQuery(baseQuery: CommonBaseQuery::class)]
#[AdditionalDataProvider(additionalDataProviders: [AdditionalDataProviderFromTableReferences::class])]
class UserEntity extends AbstractEntity {

  use EntityPropertyTrait;

  private $usr_id;

  private $firstname;

  private $lastname;

  private $email;

  private $active;

  private $inactivation_date;

  private $roles;

}