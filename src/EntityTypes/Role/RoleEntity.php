<?php

namespace App\EntityTypes\Role;

use App\Database\BaseQuery\BaseQueryDefinition;
use App\Database\BaseQuery\CommonBaseQuery;
use App\Database\DaViDatabaseOne;
use App\DaViEntity\AbstractEntity;
use App\DaViEntity\Schema\Attribute\DatabaseDefinition;
use App\DaViEntity\Schema\Attribute\EntityTypeDefinition;
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
use App\EntityTypes\RoleUserMap\RoleUserMapEntity;
use App\Item\ItemHandler_AdditionalData\AggregationFilterAdditionalDataItemHandler;
use App\Item\ItemHandler_AdditionalData\Attribute\AggregationAdditionalDataHandlerDefinition;
use App\Item\ItemInterface;
use App\Item\Property\Attribute\DatabaseColumnDefinition;
use App\Item\Property\Attribute\EntityOverviewPropertyDefinition;
use App\Item\Property\Attribute\LabelPropertyDefinition;
use App\Item\Property\Attribute\PropertyDefinition;
use App\Item\Property\Attribute\PropertyPreDefinedDefinition;
use App\Item\Property\Attribute\SearchPropertyDefinition;
use App\Item\Property\Attribute\UniquePropertyDefinition;
use App\Item\Property\PreDefinedAttributes\PreDefined;
use App\Item\Property\PropertyItemInterface;

#[RepositoryDefinition(repositoryClass: RoleRepository::class)]
#[EntityTypeDefinition(name: 'Role', label: 'Rolle'),
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
    #[PropertyDefinition(
        dataType: ItemInterface::DATA_TYPE_INTEGER,
        label: 'Rollen ID',
      ),
      EntityOverviewPropertyDefinition(rank: 10),
      DatabaseColumnDefinition,
      UniquePropertyDefinition,
    ]
    #[PropertyPreDefinedDefinition([
      [PreDefined::class, 'integer'],
    ])]
    private PropertyItemInterface $rol_id;
  /** ################### */

  /** ########################################################## title */
    #[PropertyDefinition(
      dataType: ItemInterface::DATA_TYPE_STRING,
      label: 'Rollentitel',
    ),
      LabelPropertyDefinition,
      SearchPropertyDefinition,
      EntityOverviewPropertyDefinition,
      DatabaseColumnDefinition,
    ]
    #[PropertyPreDefinedDefinition([
      [PreDefined::class, 'string'],
    ])]
    private PropertyItemInterface $title;
  /** ################### */

  /** ########################################################## description */
    #[PropertyDefinition(
      dataType: ItemInterface::DATA_TYPE_STRING
    ),
      SearchPropertyDefinition,
      EntityOverviewPropertyDefinition,
      DatabaseColumnDefinition,
    ]
    #[PropertyPreDefinedDefinition([
      [PreDefined::class, 'string'],
    ])]
    private PropertyItemInterface $description;
  /** ################### */

  /** ########################################################## count_users */
    #[PropertyDefinition(
      dataType: ItemInterface::DATA_TYPE_INTEGER,
      label: 'Anzahl Benutzer'
    )]
    #[PropertyPreDefinedDefinition([
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
  /** ################### */

  /** ########################################################## count_user_status */
    #[PropertyDefinition(
      dataType: ItemInterface::DATA_TYPE_TABLE,
      label: 'Anzahl Benutzer nach Status'
    )]
    #[PropertyPreDefinedDefinition([
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
  /** ################### */

}