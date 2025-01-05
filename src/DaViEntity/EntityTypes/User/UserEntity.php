<?php

namespace App\DaViEntity\EntityTypes\User;

use App\Database\BaseQuery\BaseQuery;
use App\Database\BaseQuery\CommonBaseQuery;
use App\DaViEntity\AbstractEntity;
use App\DaViEntity\AdditionalData\AdditionalDataProvider;
use App\DaViEntity\AdditionalData\AdditionalDataProviderFromTableReferences;
use App\DaViEntity\Attribute\EntityType;
use App\DaViEntity\EntityColumnBuilder\CommonEntityColumnBuilder;
use App\DaViEntity\EntityColumnBuilder\EntityColumnBuilder;
use App\DaViEntity\EntityCreator\CommonEntityCreator;
use App\DaViEntity\EntityCreator\EntityCreator;
use App\DaViEntity\EntityDataProvider\CommonSqlEntityDataProvider;
use App\DaViEntity\EntityDataProvider\EntityDataProvider;
use App\DaViEntity\EntityListProvider\CommonEntityListProvider;
use App\DaViEntity\EntityListProvider\EntityListProvider;
use App\DaViEntity\EntityListSearch\CommonEntitySearch;
use App\DaViEntity\EntityListSearch\EntityListSearch;
use App\DaViEntity\EntityRefiner\CommonEntityRefiner;
use App\DaViEntity\EntityRefiner\EntityRefiner;
use App\DaViEntity\EntityRepository\EntityRepository;
use App\DaViEntity\Traits\EntityPropertyTrait;

#[EntityType(name: 'User')]
#[EntityRepository(entityRepositoryClass: UserRepository::class)]
#[BaseQuery(baseQuery: CommonBaseQuery::class)]
#[AdditionalDataProvider(additionalDataProviders: [AdditionalDataProviderFromTableReferences::class])]
#[EntityListSearch(entityListSearch: CommonEntitySearch::class)]
#[EntityDataProvider(dataProviderClass: CommonSqlEntityDataProvider::class)]
#[EntityCreator(entityCreator: CommonEntityCreator::class)]
#[EntityRefiner(entityRefinerClass: CommonEntityRefiner::class)]
#[EntityColumnBuilder(entityColumnBuilderClass: CommonEntityColumnBuilder::class)]
#[EntityListProvider(entityListClass: CommonEntityListProvider::class)]
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