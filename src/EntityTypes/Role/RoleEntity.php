<?php

namespace App\EntityTypes\Role;

use App\Database\BaseQuery\BaseQueryDefinition;
use App\Database\BaseQuery\CommonBaseQuery;
use App\Database\DaViDatabaseOne;
use App\DaViEntity\AbstractEntity;
use App\DaViEntity\ColumnBuilder\CommonColumnBuilder;
use App\DaViEntity\ColumnBuilder\ColumnBuilderDefinition;
use App\DaViEntity\Creator\CommonCreator;
use App\DaViEntity\Creator\CreatorDefinition;
use App\DaViEntity\DataProvider\CommonSqlDataProvider;
use App\DaViEntity\DataProvider\DataProviderDefinition;
use App\DaViEntity\ListProvider\CommonListProvider;
use App\DaViEntity\ListProvider\ListProviderDefinition;
use App\DaViEntity\OverviewBuilder\CommonOverviewBuilder;
use App\DaViEntity\OverviewBuilder\OverviewBuilderDefinition;
use App\DaViEntity\SimpleSearch\CommonSimpleSearch;
use App\DaViEntity\SimpleSearch\SimpleSearchDefinition;
use App\DaViEntity\Refiner\CommonRefiner;
use App\DaViEntity\Refiner\RefinerDefinition;
use App\DaViEntity\Repository\RepositoryDefinition;
use App\DaViEntity\Schema\Attribute\DatabaseDefinition;
use App\DaViEntity\Schema\Attribute\EntityTypeAttr;
use App\DaViEntity\Traits\EntityPropertyTrait;
use App\DaViEntity\Validator\ValidatorBase;
use App\DaViEntity\Validator\ValidatorDefinition;
use App\DaViEntity\ViewBuilder\CommonViewBuilder;
use App\DaViEntity\ViewBuilder\ViewBuilderDefinition;
use App\EntityServices\AggregatedData\SqlAggregatedDataProvider;
use App\EntityServices\AggregatedData\SqlAggregatedDataProviderDefinition;
use App\EntityServices\EntityLabel\CommonLabelCrafter;
use App\EntityServices\EntityLabel\LabelCrafterDefinition;
use App\EntityTypes\RoleUserMap\RoleUserMapEntity;
use App\Item\ItemHandler_AdditionalData\AggregationFilterAdditionalDataItemHandler;
use App\Item\ItemHandler_AdditionalData\Attribute\AggregationAdditionalDataHandlerDefinition;
use App\Item\ItemInterface;
use App\Item\Property\Attribute\DatabaseColumnDefinition;
use App\Item\Property\Attribute\EntityOverviewPropertyAttr;
use App\Item\Property\Attribute\LabelPropertyAttr;
use App\Item\Property\Attribute\PropertyAttr;
use App\Item\Property\Attribute\PropertyPreDefinedAttr;
use App\Item\Property\Attribute\SearchPropertyDefinition;
use App\Item\Property\Attribute\UniquePropertyDefinition;
use App\Item\Property\PreDefinedAttributes\PreDefined;
use App\Item\Property\PropertyItemInterface;
use Symfony\Component\Validator\ValidatorBuilder;

#[RepositoryDefinition(repositoryClass: RoleRepository::class)]
#[EntityTypeAttr(name: 'Role', label: 'Role'),
]
#[BaseQueryDefinition(baseQueryClass: CommonBaseQuery::class),
  ColumnBuilderDefinition(columnBuilderClass: CommonColumnBuilder::class),
  RefinerDefinition(refinerClass: CommonRefiner::class),
  CreatorDefinition(creatorClass: CommonCreator::class),
  SimpleSearchDefinition(simpleSearchClass: CommonSimpleSearch::class),
  DataProviderDefinition(dataProviderClass: CommonSqlDataProvider::class),
  ListProviderDefinition(listProviderClass: CommonListProvider::class),
  OverviewBuilderDefinition(overviewBuilderClass: CommonOverviewBuilder::class),
  ViewBuilderDefinition(viewBuilderClass: CommonViewBuilder::class),
  SqlAggregatedDataProviderDefinition(aggregatedDataProviderClass: SqlAggregatedDataProvider::class),
  LabelCrafterDefinition(labelCrafterClass: CommonLabelCrafter::class),
  ValidatorDefinition(validatorClass: ValidatorBase::class),
]
#[DatabaseDefinition(
  databaseClass: DaViDatabaseOne::class,
  baseTable: 'role'),
]
class RoleEntity extends AbstractEntity {

  use EntityPropertyTrait;

  /** ########################################################## rol_id */
    #[PropertyAttr(dataType: ItemInterface::DATA_TYPE_INTEGER),
      EntityOverviewPropertyAttr(rank: 10),
      DatabaseColumnDefinition,
      UniquePropertyDefinition
    ]
    #[PropertyPreDefinedAttr([
      [PreDefined::class, 'integer'],
    ])]
    private PropertyItemInterface $rol_id;
  /** ########################################################## */

  /** ########################################################## title */
    #[PropertyAttr(
      dataType: ItemInterface::DATA_TYPE_STRING
    ),
      LabelPropertyAttr,
      SearchPropertyDefinition,
      EntityOverviewPropertyAttr,
      DatabaseColumnDefinition,
    ]
    #[PropertyPreDefinedAttr([
      [PreDefined::class, 'string'],
    ])]
    private PropertyItemInterface $title;
  /** ########################################################## */

  /** ########################################################## description */
    #[PropertyAttr(
      dataType: ItemInterface::DATA_TYPE_STRING
    ),
      SearchPropertyDefinition,
      EntityOverviewPropertyAttr,
      DatabaseColumnDefinition,
    ]
    #[PropertyPreDefinedAttr([
      [PreDefined::class, 'string'],
    ])]
    private PropertyItemInterface $description;
  /** ########################################################## */

  /** ########################################################## count_users */
    #[PropertyAttr(
      dataType: ItemInterface::DATA_TYPE_INTEGER,
      label: 'Anzahl Benutzer'
    )]
    #[PropertyPreDefinedAttr([
      [PreDefined::class, 'simpleInteger'],
    ])]
    #[AggregationAdditionalDataHandlerDefinition(
      handlerClass: AggregationFilterAdditionalDataItemHandler::class,
      targetEntityClass: RoleUserMapEntity::class,
      aggregationKey: 'count_users',
      filters: [
        'role' => [
            'filter' => 'rol_id',
            'filterMapping' => 'rol_id',
          ]
      ],
    )]
    private PropertyItemInterface $count_user;
  /** ########################################################## */

  /** ########################################################## count_user_status */
    #[PropertyAttr(
      dataType: ItemInterface::DATA_TYPE_TABLE,
      label: 'Anzahl Benutzer nach Status'
    )]
    #[PropertyPreDefinedAttr([
      [PreDefined::class, 'table'],
    ])]
    #[AggregationAdditionalDataHandlerDefinition(
      handlerClass: AggregationFilterAdditionalDataItemHandler::class,
      targetEntityClass: RoleUserMapEntity::class,
      aggregationKey: 'count_users_status',
      filters: [
        'role' => [
          'filter' => 'rol_id',
          'filterMapping' => 'rol_id',
        ]
      ],
      propertyBlacklist: ['rol_id']
    )]
    private PropertyItemInterface $count_user_status;
  /** ########################################################## */

}