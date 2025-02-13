<?php

namespace App\EntityTypes\RoleUserMap;

use App\Database\BaseQuery\BaseQuery;
use App\Database\BaseQuery\CommonBaseQuery;
use App\Database\DaViDatabaseOne;
use App\Database\SqlFilterHandler\Attribute\SqlFilterDefinitionAttr;
use App\Database\SqlFilterHandler\EntityReferenceFilterHandler;
use App\DaViEntity\AbstractEntity;
use App\DaViEntity\ColumnBuilder\CommonColumnBuilder;
use App\DaViEntity\ColumnBuilder\ColumnBuilderDefinition;
use App\DaViEntity\Creator\CommonCreator;
use App\DaViEntity\Creator\CreatorDefinition;
use App\DaViEntity\DataProvider\CommonSqlDataProvider;
use App\DaViEntity\DataProvider\DataProviderDefinition;
use App\DaViEntity\ListProvider\CommonListProvider;
use App\DaViEntity\ListProvider\ListProviderDefinition;
use App\DaViEntity\Search\CommonSearch;
use App\DaViEntity\Search\SearchDefinition;
use App\DaViEntity\Refiner\CommonRefiner;
use App\DaViEntity\Refiner\RefinerDefinition;
use App\DaViEntity\Repository\RepositoryDefinition;
use App\DaViEntity\Schema\Attribute\DatabaseAttr;
use App\DaViEntity\Schema\Attribute\EntityTypeAttr;
use App\DaViEntity\Traits\EntityPropertyTrait;
use App\Item\Property\Attribute\EntityOverviewPropertyAttr;
use App\Item\Property\Attribute\LabelPropertyAttr;
use App\Item\Property\Attribute\PropertyPreDefinedAttr;
use App\Item\Property\Attribute\UniquePropertyDefinition;
use App\Item\Property\PreDefinedAttributes\PreDefined;

#[RepositoryDefinition(repositoryClass: RoleUserMapRepository::class)]
#[EntityTypeAttr(name: 'RoleUserMap', label: 'Rolle/User Map')]
#[BaseQuery(baseQuery: CommonBaseQuery::class),
  SearchDefinition(entityListSearch: CommonSearch::class),
  DataProviderDefinition(dataProviderClass: CommonSqlDataProvider::class),
  CreatorDefinition(creatorClass: CommonCreator::class),
  RefinerDefinition(refinerClass: CommonRefiner::class),
  ColumnBuilderDefinition(entityColumnBuilderClass: CommonColumnBuilder::class),
  ListProviderDefinition(listProviderClass: CommonListProvider::class)
]
#[DatabaseAttr(
  databaseClass: DaViDatabaseOne::class,
  baseTable: 'role_user_map'),
]
class RoleUserMapEntity extends AbstractEntity {

  use EntityPropertyTrait;

  #[UniquePropertyDefinition,
    LabelPropertyAttr,
    EntityOverviewPropertyAttr
  ]
  #[PropertyPreDefinedAttr([
    [PreDefined::class, 'integer'],
  ])]
  #[SqlFilterDefinitionAttr(
    filterHandler: EntityReferenceFilterHandler::class,
    title: 'Benutzer suchen',
    description: 'Alle Rollen des Nutzers anzeigen',
    group: false
  )]
  private $usr_id;

  #[UniquePropertyDefinition,
    LabelPropertyAttr,
    EntityOverviewPropertyAttr
  ]
  #[PropertyPreDefinedAttr([
    [PreDefined::class, 'integer'],
  ])]
  #[SqlFilterDefinitionAttr(
    filterHandler: EntityReferenceFilterHandler::class,
    title: 'Rollen suchen',
    description: 'Alle Benutzer der Rolle anzeigen',
    group: false
  )]
  private $rol_id;

}