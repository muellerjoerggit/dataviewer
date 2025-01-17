<?php

namespace App\DaViEntity\EntityTypes\Role;

use App\Database\BaseQuery\BaseQuery;
use App\Database\BaseQuery\CommonBaseQuery;
use App\Database\DaViDatabaseOne;
use App\DaViEntity\AbstractEntity;
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
use App\Item\ItemInterface;
use App\Item\Property\Attribute\DatabaseColumnAttr;
use App\Item\Property\Attribute\EntityOverviewPropertyAttr;
use App\Item\Property\Attribute\LabelPropertyAttr;
use App\Item\Property\Attribute\PropertyAttr;
use App\Item\Property\Attribute\SearchPropertyAttr;
use App\Item\Property\Attribute\UniquePropertyAttr;
use App\Item\Property\PropertyItemInterface;

#[EntityRepository(entityRepositoryClass: RoleRepository::class)]
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
#[DatabaseAttr(databaseClass: DaViDatabaseOne::class, baseTable: 'role'),
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