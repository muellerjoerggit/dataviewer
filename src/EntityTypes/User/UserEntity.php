<?php

namespace App\EntityTypes\User;

use App\Database\BaseQuery\BaseQuery;
use App\Database\BaseQuery\CommonBaseQuery;
use App\Database\DaViDatabaseOne;
use App\Database\TableReferenceHandler\Attribute\CommonTableReferenceAttr;
use App\Database\TableReferenceHandler\CommonTableReferenceHandler;
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
use App\DaViEntity\EntityRepository\EntityRepositoryAttr;
use App\DaViEntity\Schema\Attribute\DatabaseAttr;
use App\DaViEntity\Schema\Attribute\EntityTypeAttr;
use App\DaViEntity\Traits\EntityPropertyTrait;
use App\EntityTypes\Role\RoleEntity;
use App\EntityTypes\RoleUserMap\RoleUserMapEntity;
use App\Item\ItemInterface;
use App\Item\Property\Attribute\EntityOverviewPropertyAttr;
use App\Item\Property\Attribute\LabelPropertyAttr;
use App\Item\Property\Attribute\PropertyAttr;
use App\Item\Property\Attribute\SearchPropertyAttr;
use App\Item\Property\Attribute\UniquePropertyAttr;

#[EntityRepositoryAttr(entityRepositoryClass: UserRepository::class)]
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
#[CommonTableReferenceAttr(
  name: 'roleMapping',
  handlerClass: CommonTableReferenceHandler::class,
  toEntityClass: RoleUserMapEntity::class,
  propertyConditions: ['usr_id' => 'usr_id'])
]
class UserEntity extends AbstractEntity {

  use EntityPropertyTrait;

  #[PropertyAttr(
    dataType: ItemInterface::DATA_TYPE_INTEGER
  )]
  #[UniquePropertyAttr,
    LabelPropertyAttr(rank: 10),
    EntityOverviewPropertyAttr(rank: 10)
  ]
  private $usr_id;

  #[PropertyAttr(
    dataType: ItemInterface::DATA_TYPE_STRING,
    label: 'Vorname'
  )]
  #[LabelPropertyAttr(rank: 20),
    EntityOverviewPropertyAttr(rank: 20),
    SearchPropertyAttr
  ]
  private $firstname;

  #[PropertyAttr(
    dataType: ItemInterface::DATA_TYPE_STRING,
    label: 'Nachname'
  )]
  #[LabelPropertyAttr(rank: 30),
    EntityOverviewPropertyAttr(rank: 30),
    SearchPropertyAttr
  ]
  private $lastname;

  #[PropertyAttr(
    dataType: ItemInterface::DATA_TYPE_STRING,
    label: 'E-Mail'
  )]
  #[SearchPropertyAttr
  ]
  private $email;

  #[PropertyAttr(
    dataType: ItemInterface::DATA_TYPE_BOOL,
    label: 'Aktiv'
  )]
  #[EntityOverviewPropertyAttr(rank: 40)
  ]
  private $active;

  #[PropertyAttr(
    dataType: ItemInterface::DATA_TYPE_DATE_TIME,
    label: 'Inaktivierungsdatum'
  )]
  private $inactivation_date;

  #[PropertyAttr(
    dataType: ItemInterface::DATA_TYPE_INTEGER,
    label: 'Rollen',
    cardinality: ItemInterface::CARDINALITY_MULTIPLE
  )]
  private $roles;

  #[PropertyAttr(
    dataType: ItemInterface::DATA_TYPE_STRING,
    label: 'zweite E-Mail'
  )]
  private $second_mail;

}