<?php

namespace App\EntityTypes\RoleUserMap;

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
use App\Item\Property\Attribute\EntityOverviewPropertyAttr;
use App\Item\Property\Attribute\LabelPropertyAttr;
use App\Item\Property\Attribute\UniquePropertyAttr;

#[EntityRepositoryAttr(entityRepositoryClass: RoleUserMapRepository::class)]
#[EntityTypeAttr(name: 'RoleUserMap', label: 'Rolle/User Map')]
#[BaseQuery(baseQuery: CommonBaseQuery::class),
  EntityListSearch(entityListSearch: CommonEntitySearch::class),
  EntityDataProvider(dataProviderClass: CommonSqlEntityDataProvider::class),
  EntityCreator(entityCreator: CommonEntityCreator::class),
  EntityRefiner(entityRefinerClass: CommonEntityRefiner::class),
  EntityColumnBuilder(entityColumnBuilderClass: CommonEntityColumnBuilder::class),
  EntityListProvider(entityListClass: CommonEntityListProvider::class)
]
#[DatabaseAttr(
  databaseClass: DaViDatabaseOne::class,
  baseTable: 'role_user_map'),
]
class RoleUserMapEntity extends AbstractEntity {

  use EntityPropertyTrait;

  #[UniquePropertyAttr,
    LabelPropertyAttr,
    EntityOverviewPropertyAttr
  ]
  private $usr_id;

  #[UniquePropertyAttr,
    LabelPropertyAttr,
    EntityOverviewPropertyAttr
  ]
  private $rol_id;

}