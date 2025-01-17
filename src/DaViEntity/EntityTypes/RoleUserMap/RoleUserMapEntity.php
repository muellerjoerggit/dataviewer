<?php

namespace App\DaViEntity\EntityTypes\RoleUserMap;

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
use App\Item\Property\Attribute\EntityOverviewPropertyAttr;
use App\Item\Property\Attribute\LabelPropertyAttr;
use App\Item\Property\Attribute\UniquePropertyAttr;

#[EntityRepository(entityRepositoryClass: RoleUserMapRepository::class)]
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