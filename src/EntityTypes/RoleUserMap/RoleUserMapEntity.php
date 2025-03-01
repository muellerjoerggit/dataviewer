<?php

namespace App\EntityTypes\RoleUserMap;

use App\Database\AggregationHandler\Attribute\CountAggregationHandlerDefinition;
use App\Database\AggregationHandler\Attribute\CountGroupAggregationHandlerDefinition;
use App\Database\AggregationHandler\CountAggregationHandler;
use App\Database\AggregationHandler\CountGroupAggregationHandler;
use App\Database\BaseQuery\BaseQueryDefinition;
use App\Database\BaseQuery\CommonBaseQuery;
use App\Database\DaViDatabaseOne;
use App\Database\SqlFilterHandler\Attribute\SqlFilterDefinitionAttr;
use App\Database\SqlFilterHandler\EntityReferenceFilterHandler;
use App\DaViEntity\AbstractEntity;
use App\DaViEntity\Schema\Attribute\DatabaseDefinition;
use App\DaViEntity\Schema\Attribute\EntityTypeAttr;
use App\DaViEntity\Traits\EntityPropertyTrait;
use App\EntityServices\AggregatedData\SqlAggregatedDataProvider;
use App\EntityServices\AggregatedData\SqlAggregatedDataProviderDefinition;
use App\EntityServices\ColumnBuilder\ColumnBuilderDefinition;
use App\EntityServices\ColumnBuilder\CommonColumnBuilder;
use App\EntityServices\Creator\CommonCreator;
use App\EntityServices\Creator\CreatorDefinition;
use App\EntityServices\DataProvider\CommonSqlDataProvider;
use App\EntityServices\DataProvider\DataProviderDefinition;
use App\EntityServices\EntityLabel\CommonLabelCrafter;
use App\EntityServices\EntityLabel\LabelCrafterDefinition;
use App\EntityServices\ListProvider\CommonListProvider;
use App\EntityServices\ListProvider\ListProviderDefinition;
use App\EntityServices\OverviewBuilder\CommonOverviewBuilder;
use App\EntityServices\OverviewBuilder\OverviewBuilderDefinition;
use App\EntityServices\Refiner\CommonRefiner;
use App\EntityServices\Refiner\RefinerDefinition;
use App\EntityServices\Repository\RepositoryDefinition;
use App\EntityServices\SimpleSearch\CommonSimpleSearch;
use App\EntityServices\SimpleSearch\SimpleSearchDefinition;
use App\EntityServices\Validator\ValidatorBase;
use App\EntityServices\Validator\ValidatorDefinition;
use App\EntityServices\ViewBuilder\CommonViewBuilder;
use App\EntityServices\ViewBuilder\ViewBuilderDefinition;
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
  ListProviderDefinition(listProviderClass: CommonListProvider::class),
  OverviewBuilderDefinition(overviewBuilderClass: CommonOverviewBuilder::class),
  ViewBuilderDefinition(viewBuilderClass: CommonViewBuilder::class),
  SqlAggregatedDataProviderDefinition(aggregatedDataProviderClass: SqlAggregatedDataProvider::class),
  LabelCrafterDefinition(labelCrafterClass: CommonLabelCrafter::class),
  ValidatorDefinition(validatorClass: ValidatorBase::class),
]
#[DatabaseDefinition(
  databaseClass: DaViDatabaseOne::class,
  baseTable: 'role_user_map'),
]

#[CountAggregationHandlerDefinition(
    name: 'count_users',
    aggregationHandlerClass: CountAggregationHandler::class,
    title: 'Anzahl Benutzer nach Rollen',
    description: 'Anzahl Benutzer mit der Rolle',
    labelCountColumn: 'Anzahl Benutzer',
  ),
  CountGroupAggregationHandlerDefinition(
    name: 'count_users_status',
    aggregationHandlerClass: CountGroupAggregationHandler::class,
    header: [
      'role' => 'Rolle',
      'active' => 'Benutzer Status',
    ],
    properties: [
      'usr_id.active' => 'active',
      'rol_id' => 'role',
    ],
    labelCountColumn: 'Anzahl Benutzer',
    title: 'Anzahl Benutzer mit der Rolle / Status',
    description: 'Anzahl Benutzer mit der Rolle und Status'
  )
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
    key: 'usr_id',
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
    key: 'rol_id',
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