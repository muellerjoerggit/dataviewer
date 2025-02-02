<?php

namespace App\EntityTypes\Role;

use App\Database\BaseQuery\BaseQuery;
use App\Database\BaseQuery\CommonBaseQuery;
use App\Database\DaViDatabaseOne;
use App\DaViEntity\AbstractEntity;
use App\DaViEntity\ColumnBuilder\CommonEntityColumnBuilder;
use App\DaViEntity\ColumnBuilder\EntityColumnBuilder;
use App\DaViEntity\Creator\CommonEntityCreator;
use App\DaViEntity\Creator\EntityCreator;
use App\DaViEntity\DataProvider\CommonSqlEntityDataProvider;
use App\DaViEntity\DataProvider\EntityDataProvider;
use App\DaViEntity\ListProvider\CommonEntityListProvider;
use App\DaViEntity\ListProvider\EntityListProvider;
use App\DaViEntity\ListSearch\CommonEntitySearch;
use App\DaViEntity\ListSearch\EntityListSearch;
use App\DaViEntity\Refiner\CommonEntityRefiner;
use App\DaViEntity\Refiner\EntityRefiner;
use App\DaViEntity\Repository\EntityRepositoryAttr;
use App\DaViEntity\Schema\Attribute\DatabaseAttr;
use App\DaViEntity\Schema\Attribute\EntityTypeAttr;
use App\DaViEntity\Traits\EntityPropertyTrait;
use App\Item\ItemInterface;
use App\Item\Property\Attribute\DatabaseColumnAttr;
use App\Item\Property\Attribute\EntityOverviewPropertyAttr;
use App\Item\Property\Attribute\LabelPropertyAttr;
use App\Item\Property\Attribute\PropertyAttr;
use App\Item\Property\Attribute\SearchPropertyAttr;
use App\Item\Property\Attribute\UniquePropertyAttr;
use App\Item\Property\PropertyItemInterface;

#[EntityRepositoryAttr(entityRepositoryClass: RoleRepository::class)]
#[EntityTypeAttr(name: 'Role', label: 'Role'),
]
#[BaseQuery(baseQuery: CommonBaseQuery::class),
  EntityColumnBuilder(entityColumnBuilderClass: CommonEntityColumnBuilder::class),
  EntityRefiner(entityRefinerClass: CommonEntityRefiner::class),
  EntityCreator(entityCreator: CommonEntityCreator::class),
  EntityListSearch(entityListSearch: CommonEntitySearch::class),
  EntityDataProvider(dataProviderClass: CommonSqlEntityDataProvider::class),
  EntityListProvider(entityListClass: CommonEntityListProvider::class),
]
#[DatabaseAttr(
  databaseClass: DaViDatabaseOne::class,
  baseTable: 'role'),
]
class RoleEntity extends AbstractEntity {

  use EntityPropertyTrait;

  #[PropertyAttr(dataType: ItemInterface::DATA_TYPE_INTEGER),
    EntityOverviewPropertyAttr,
    DatabaseColumnAttr,
    UniquePropertyAttr
  ]
  private PropertyItemInterface $rol_id;

  #[PropertyAttr(
    dataType: ItemInterface::DATA_TYPE_STRING
  ),
    LabelPropertyAttr,
    SearchPropertyAttr,
    EntityOverviewPropertyAttr,
    DatabaseColumnAttr,
  ]
  private PropertyItemInterface $title;

  #[PropertyAttr(
    dataType: ItemInterface::DATA_TYPE_STRING
  ),
    SearchPropertyAttr,
    EntityOverviewPropertyAttr,
    DatabaseColumnAttr,
  ]
  private PropertyItemInterface $description;

  #[PropertyAttr(
    dataType: ItemInterface::DATA_TYPE_TABLE,
    label: 'Anzahl Benutzer'
  )]
  private PropertyItemInterface $count_user;

  #[PropertyAttr(
    dataType: ItemInterface::DATA_TYPE_TABLE,
    label: 'Anzahl Benutzer nach Status'
  )]
  private PropertyItemInterface $count_user_status;

}