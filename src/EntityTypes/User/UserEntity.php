<?php

namespace App\EntityTypes\User;

use App\Database\BaseQuery\BaseQueryDefinition;
use App\Database\BaseQuery\CommonBaseQuery;
use App\Database\DaViDatabaseOne;
use App\Database\TableReference\TableReferencePropertyDefinition;
use App\Database\TableReferenceHandler\Attribute\CommonTableReferenceDefinition;
use App\Database\TableReferenceHandler\CommonTableReferenceHandler;
use App\DaViEntity\AbstractEntity;
use App\DaViEntity\AdditionalData\AdditionalDataProviderDefinition;
use App\DaViEntity\AdditionalData\AdditionalDataProviderFromTableReferences;
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
use App\DaViEntity\Validator\ValidatorDefinition;
use App\DaViEntity\ViewBuilder\CommonViewBuilder;
use App\DaViEntity\ViewBuilder\ViewBuilderDefinition;
use App\EntityServices\AggregatedData\SqlAggregatedDataProvider;
use App\EntityServices\AggregatedData\SqlAggregatedDataProviderDefinition;
use App\EntityServices\EntityLabel\CommonLabelCrafter;
use App\EntityServices\EntityLabel\LabelCrafterDefinition;
use App\EntityTypes\Role\RoleEntity;
use App\EntityTypes\RoleUserMap\RoleUserMapEntity;
use App\Item\ItemHandler_EntityReference\Attribute\EntityReferenceItemHandlerDefinition;
use App\Item\ItemHandler_EntityReference\CommonEntityReferenceItemHandler;
use App\Item\ItemHandler_Formatter\Attribute\FormatterItemHandlerDefinition;
use App\Item\ItemHandler_Formatter\OptionsFormatterItemHandler;
use App\Item\ItemHandler_PreRendering\Attribute\PreRenderingItemHandlerDefinition;
use App\Item\ItemHandler_PreRendering\EntityReferencePreRenderingItemHandler;
use App\Item\ItemInterface;
use App\Item\Property\Attribute\DatabaseColumnDefinition;
use App\Item\Property\Attribute\EntityOverviewPropertyAttr;
use App\Item\Property\Attribute\LabelPropertyAttr;
use App\Item\Property\Attribute\OptionItemSettingDefinition;
use App\Item\Property\Attribute\PropertyAttr;
use App\Item\Property\Attribute\PropertyPreDefinedAttr;
use App\Item\Property\Attribute\SearchPropertyDefinition;
use App\Item\Property\Attribute\UniquePropertyDefinition;
use App\Item\Property\PreDefinedAttributes\PreDefined;
use App\Services\EntityActionHandler\UrlActionDefinitionAttr;
use App\Services\EntityActionHandler\UrlActionHandler;

#[RepositoryDefinition(repositoryClass: UserRepository::class)]
#[EntityTypeAttr(name: 'User', label: 'Benutzer')]
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
  ValidatorDefinition(validatorClass: UserValidator::class),
]
#[AdditionalDataProviderDefinition(additionalDataProviderClass: AdditionalDataProviderFromTableReferences::class)]
#[DatabaseDefinition(
  databaseClass: DaViDatabaseOne::class,
  baseTable: 'usr_data'),
]

#[CommonTableReferenceDefinition(
  name: 'roleMapping',
  handlerClass: CommonTableReferenceHandler::class,
  toEntityClass: RoleUserMapEntity::class,
  propertyConditions: ['usr_id' => 'usr_id'])
]

#[UrlActionDefinitionAttr(
  handler: UrlActionHandler::class,
  urlTemplate: 'www.example.com/user/{user}',
  placeholders: ['user' => 'usr_id'],
  externalUrl: TRUE,
  title: 'Example Url',
  description: 'Beispiel für eine URL Action'
)]
class UserEntity extends AbstractEntity {

  use EntityPropertyTrait;

  #[PropertyAttr(
      dataType: ItemInterface::DATA_TYPE_INTEGER
    ),
    DatabaseColumnDefinition
  ]
  #[UniquePropertyDefinition,
    LabelPropertyAttr(rank: 10),
    EntityOverviewPropertyAttr(rank: 10)
  ]
  #[PropertyPreDefinedAttr([
    [PreDefined::class, 'integer'],
  ])]
  private $usr_id;


  #[PropertyAttr(
      dataType: ItemInterface::DATA_TYPE_STRING,
      label: 'Vorname'
    ),
    DatabaseColumnDefinition
  ]
  #[LabelPropertyAttr(rank: 20),
    EntityOverviewPropertyAttr(rank: 20),
    SearchPropertyDefinition
  ]
  #[PropertyPreDefinedAttr([
    [PreDefined::class, 'string'],
  ])]
  private $firstname;


  #[PropertyAttr(
      dataType: ItemInterface::DATA_TYPE_STRING,
      label: 'Nachname'
    ),
    DatabaseColumnDefinition
  ]
  #[LabelPropertyAttr(rank: 30),
    EntityOverviewPropertyAttr(rank: 30),
    SearchPropertyDefinition
  ]
  #[PropertyPreDefinedAttr([
    [PreDefined::class, 'string'],
  ])]
  private $lastname;


  #[PropertyAttr(
      dataType: ItemInterface::DATA_TYPE_STRING,
      label: 'E-Mail'
    ),
    DatabaseColumnDefinition
  ]
  #[SearchPropertyDefinition
  ]
  #[PropertyPreDefinedAttr([
    [PreDefined::class, 'string'],
  ])]
  private $email;


  #[PropertyAttr(
      dataType: ItemInterface::DATA_TYPE_BOOL,
      label: 'Aktiv'
    ),
    DatabaseColumnDefinition
  ]
  #[EntityOverviewPropertyAttr(rank: 40)
  ]
  #[OptionItemSettingDefinition(
    options: [
      0 => ['label' => 'inactive'],
      1 => ['label' => 'active']
    ]
  )]
  #[PropertyPreDefinedAttr([
    [PreDefined::class, 'integer'],
  ])]
  #[FormatterItemHandlerDefinition(handlerClass: OptionsFormatterItemHandler::class)]
  private $active;


  #[PropertyAttr(
      dataType: ItemInterface::DATA_TYPE_DATE_TIME,
      label: 'Inaktivierungsdatum'
    ),
    DatabaseColumnDefinition
  ]
  #[PropertyPreDefinedAttr([
    [PreDefined::class, 'dateTime'],
  ])]
  private $inactivation_date;


  #[PropertyAttr(
      dataType: ItemInterface::DATA_TYPE_INTEGER,
      label: 'Rollen',
      cardinality: ItemInterface::CARDINALITY_MULTIPLE
    ),
  ]
  #[TableReferencePropertyDefinition(
    tableReferenceName: 'roleMapping',
    property: 'rol_id',
  )]
  #[PropertyPreDefinedAttr([
    [PreDefined::class, 'integer'],
  ])]
  #[EntityReferenceItemHandlerDefinition(
      handlerClass: CommonEntityReferenceItemHandler::class,
      targetEntity: RoleEntity::class,
      targetProperty: 'rol_id',
    ),
    PreRenderingItemHandlerDefinition(handlerClass: EntityReferencePreRenderingItemHandler::class)
  ]
  private $roles;


//  #[PropertyAttr(
//      dataType: ItemInterface::DATA_TYPE_STRING,
//      label: 'zweite E-Mail'
//    ),
//    DatabaseColumnAttr
//  ]
//  #[PropertyPreDefinedAttr([
//    [PreDefined::class, 'string'],
//  ])]
//  private $second_mail;

}