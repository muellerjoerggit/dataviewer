<?php

namespace App\EntityTypes\User;

use App\Database\BaseQuery\BaseQuery;
use App\Database\BaseQuery\CommonBaseQuery;
use App\Database\DaViDatabaseOne;
use App\DaViEntity\AbstractEntity;
use App\DaViEntity\AdditionalData\AdditionalDataProvider;
use App\DaViEntity\AdditionalData\AdditionalDataProviderFromTableReferences;
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
use App\DaViEntity\Schema\Attribute\DatabaseAttr;
use App\DaViEntity\Schema\Attribute\EntityTypeAttr;
use App\DaViEntity\Traits\EntityPropertyTrait;
use App\Item\Property\Attribute\EntityOverviewPropertyAttr;
use App\Item\Property\Attribute\LabelPropertyAttr;
use App\Item\Property\Attribute\SearchPropertyAttr;
use App\Item\Property\Attribute\UniquePropertyAttr;

#[EntityRepository(entityRepositoryClass: UserRepository::class)]
#[EntityTypeAttr(name: 'User', label: 'Benutzer')]
#[BaseQuery(baseQuery: CommonBaseQuery::class),
  EntityListSearch(entityListSearch: CommonEntitySearch::class),
  EntityDataProvider(dataProviderClass: CommonSqlEntityDataProvider::class),
  EntityCreator(entityCreator: CommonEntityCreator::class),
  EntityRefiner(entityRefinerClass: CommonEntityRefiner::class),
  EntityColumnBuilder(entityColumnBuilderClass: CommonEntityColumnBuilder::class),
  EntityListProvider(entityListClass: CommonEntityListProvider::class)
]
#[AdditionalDataProvider(additionalDataProviders: [AdditionalDataProviderFromTableReferences::class])]
#[DatabaseAttr(
  databaseClass: DaViDatabaseOne::class,
  baseTable: 'usr_data'),
]
class UserEntity extends AbstractEntity {

  use EntityPropertyTrait;

  #[UniquePropertyAttr,
    LabelPropertyAttr(rank: 10),
    EntityOverviewPropertyAttr(rank: 10)
  ]
  private $usr_id;

  #[LabelPropertyAttr(rank: 20),
    EntityOverviewPropertyAttr(rank: 20),
    SearchPropertyAttr
  ]
  private $firstname;

  #[LabelPropertyAttr(rank: 30),
    EntityOverviewPropertyAttr(rank: 30),
    SearchPropertyAttr
  ]
  private $lastname;

  #[SearchPropertyAttr
  ]
  private $email;

  #[EntityOverviewPropertyAttr(rank: 40)
  ]
  private $active;

  private $inactivation_date;

  private $roles;

  private $second_mail;

}