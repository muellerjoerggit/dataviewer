<?php

namespace App\EntityTypes\RoleUserMap;

use App\Database\BaseQuery\BaseQueryDefinition;
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
use App\DaViEntity\SimpleSearch\CommonSimpleSearch;
use App\DaViEntity\SimpleSearch\SimpleSearchDefinition;
use App\DaViEntity\Refiner\CommonRefiner;
use App\DaViEntity\Refiner\RefinerDefinition;
use App\DaViEntity\Repository\RepositoryDefinition;
use App\DaViEntity\Schema\Attribute\DatabaseDefinition;
use App\DaViEntity\Schema\Attribute\EntityTypeAttr;
use App\DaViEntity\Traits\EntityPropertyTrait;
use App\EntityTypes\Role\RoleEntity;
use App\EntityTypes\User\UserEntity;
use App\Item\ItemHandler_EntityReference\Attribute\EntityReferenceItemHandlerDefinition;
use App\Item\ItemHandler_EntityReference\CommonEntityReferenceItemHandler;
use App\Item\ItemHandler_PreRendering\Attribute\PreRenderingItemHandlerDefinition;
use App\Item\ItemHandler_PreRendering\EntityReferencePreRenderingItemHandler;
use App\Item\ItemInterface;
use App\Item\Property\Attribute\DatabaseColumnDefinition;
use App\Item\Property\Attribute\EntityOverviewPropertyAttr;
use App\Item\Property\Attribute\LabelPropertyAttr;
use App\Item\Property\Attribute\PropertyAttr;
use App\Item\Property\Attribute\PropertyPreDefinedAttr;
use App\Item\Property\Attribute\UniquePropertyDefinition;
use App\Item\Property\PreDefinedAttributes\PreDefined;
use App\Item\Property\PropertyItemInterface;

#[RepositoryDefinition(repositoryClass: RoleUserMapRepository::class)]
#[EntityTypeAttr(name: 'RoleUserMap', label: 'Rolle/User Map')]
#[BaseQueryDefinition(baseQueryClass: CommonBaseQuery::class),
  SimpleSearchDefinition(simpleSearchClass: CommonSimpleSearch::class),
  DataProviderDefinition(dataProviderClass: CommonSqlDataProvider::class),
  CreatorDefinition(creatorClass: CommonCreator::class),
  RefinerDefinition(refinerClass: CommonRefiner::class),
  ColumnBuilderDefinition(columnBuilderClass: CommonColumnBuilder::class),
  ListProviderDefinition(listProviderClass: CommonListProvider::class)
]
#[DatabaseDefinition(
  databaseClass: DaViDatabaseOne::class,
  baseTable: 'role_user_map'),
]
class RoleUserMapEntity extends AbstractEntity {

  use EntityPropertyTrait;

  #[PropertyAttr(
    dataType: ItemInterface::DATA_TYPE_INTEGER,
    label: 'Benutzer ID',
  ),
    DatabaseColumnDefinition
  ]
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
  #[EntityReferenceItemHandlerDefinition(
    handlerClass: CommonEntityReferenceItemHandler::class,
    targetEntity: UserEntity::class,
    targetProperty: 'usr_id',
  ),
    PreRenderingItemHandlerDefinition(handlerClass: EntityReferencePreRenderingItemHandler::class)
  ]
  private PropertyItemInterface $usr_id;

  #[PropertyAttr(
    dataType: ItemInterface::DATA_TYPE_INTEGER,
    label: 'Rollen ID'
  ),
    DatabaseColumnDefinition
  ]
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
  #[EntityReferenceItemHandlerDefinition(
      handlerClass: CommonEntityReferenceItemHandler::class,
      targetEntity: RoleEntity::class,
      targetProperty: 'rol_id',
    ),
    PreRenderingItemHandlerDefinition(handlerClass: EntityReferencePreRenderingItemHandler::class)
  ]
  private PropertyItemInterface $rol_id;

}