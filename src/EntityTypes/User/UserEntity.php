<?php

namespace App\EntityTypes\User;

use App\Database\BaseQuery\BaseQueryDefinition;
use App\Database\BaseQuery\CommonBaseQuery;
use App\Database\DaViDatabaseOne;
use App\Database\TableReference\TableReferencePropertyDefinition;
use App\Database\TableReferenceHandler\Attribute\CommonTableReferenceDefinition;
use App\Database\TableReferenceHandler\CommonTableReferenceHandler;
use App\DaViEntity\AbstractEntity;
use App\DaViEntity\Schema\Attribute\DatabaseDefinition;
use App\DaViEntity\Schema\Attribute\EntityTypeDefinition;
use App\DaViEntity\Traits\EntityPropertyTrait;
use App\EntityServices\AdditionalData\AdditionalDataProviderDefinition;
use App\EntityServices\AdditionalData\AdditionalDataProviderFromTableReferences;
use App\EntityServices\AggregatedData\SqlAggregatedDataProvider;
use App\EntityServices\AggregatedData\SqlAggregatedDataProviderDefinition;
use App\EntityServices\AvailabilityVerdict\AvailabilityVerdictDefinition;
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
use App\EntityServices\Validator\ValidatorDefinition;
use App\EntityServices\ViewBuilder\CommonViewBuilder;
use App\EntityServices\ViewBuilder\ViewBuilderDefinition;
use App\EntityTypes\Role\RoleEntity;
use App\EntityTypes\RoleUserMap\RoleUserMapEntity;
use App\Item\DataType;
use App\Item\ItemHandler_EntityReference\Attribute\EntityReferenceItemHandlerDefinition;
use App\Item\ItemHandler_EntityReference\CommonEntityReferenceItemHandler;
use App\Item\ItemHandler_Formatter\Attribute\FormatterItemHandlerDefinition;
use App\Item\ItemHandler_Formatter\OptionsFormatterItemHandler;
use App\Item\ItemHandler_PreRendering\Attribute\PreRenderingItemHandlerDefinition;
use App\Item\ItemHandler_PreRendering\EntityReferencePreRenderingItemHandler;
use App\Item\ItemInterface;
use App\Item\Property\Attribute\DatabaseColumnDefinition;
use App\Item\Property\Attribute\EntityOverviewPropertyDefinition;
use App\Item\Property\Attribute\LabelPropertyDefinition;
use App\Item\Property\Attribute\OptionItemSettingDefinition;
use App\Item\Property\Attribute\PropertyDefinition;
use App\Item\Property\Attribute\PropertyPreDefinedDefinition;
use App\Item\Property\Attribute\SearchPropertyDefinition;
use App\Item\Property\Attribute\UniquePropertyDefinition;
use App\Item\Property\PreDefinedAttributes\PreDefined;
use App\Services\EntityActionHandler\UrlActionDefinitionAttr;
use App\Services\EntityActionHandler\UrlActionHandler;

#[RepositoryDefinition(repositoryClass: UserRepository::class)]
#[EntityTypeDefinition(name: 'User', label: 'Benutzer')]
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
  AvailabilityVerdictDefinition(availabilityVerdictClass: UserAvailabilityVerdict::class),
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

  #[PropertyDefinition(
      dataType: DataType::INTEGER
    ),
    DatabaseColumnDefinition
  ]
  #[UniquePropertyDefinition,
    LabelPropertyDefinition(rank: 10),
    EntityOverviewPropertyDefinition(rank: 10)
  ]
  #[PropertyPreDefinedDefinition([
    [PreDefined::class, 'integer'],
  ])]
  private $usr_id;


  #[PropertyDefinition(
      dataType: DataType::STRING,
      label: 'Vorname'
    ),
    DatabaseColumnDefinition
  ]
  #[LabelPropertyDefinition(rank: 20),
    EntityOverviewPropertyDefinition(rank: 20),
    SearchPropertyDefinition
  ]
  #[PropertyPreDefinedDefinition([
    [PreDefined::class, 'string'],
  ])]
  private $firstname;


  #[PropertyDefinition(
      dataType: DataType::STRING,
      label: 'Nachname'
    ),
    DatabaseColumnDefinition
  ]
  #[LabelPropertyDefinition(rank: 30),
    EntityOverviewPropertyDefinition(rank: 30),
    SearchPropertyDefinition
  ]
  #[PropertyPreDefinedDefinition([
    [PreDefined::class, 'string'],
  ])]
  private $lastname;


  #[PropertyDefinition(
      dataType: DataType::STRING,
      label: 'E-Mail'
    ),
    DatabaseColumnDefinition
  ]
  #[SearchPropertyDefinition
  ]
  #[PropertyPreDefinedDefinition([
    [PreDefined::class, 'string'],
  ])]
  private $email;


  #[PropertyDefinition(
      dataType: DataType::BOOL,
      label: 'Aktiv'
    ),
    DatabaseColumnDefinition
  ]
  #[EntityOverviewPropertyDefinition(rank: 40)
  ]
  #[OptionItemSettingDefinition(
    options: [
      0 => ['label' => 'inactive'],
      1 => ['label' => 'active']
    ]
  )]
  #[PropertyPreDefinedDefinition([
    [PreDefined::class, 'integer'],
  ])]
  #[FormatterItemHandlerDefinition(handlerClass: OptionsFormatterItemHandler::class)]
  private $active;


  #[PropertyDefinition(
      dataType: DataType::DATE_TIME,
      label: 'Inaktivierungsdatum'
    ),
    DatabaseColumnDefinition
  ]
  #[PropertyPreDefinedDefinition([
    [PreDefined::class, 'dateTime'],
  ])]
  private $inactivation_date;


  #[PropertyDefinition(
      dataType: DataType::INTEGER,
      label: 'Rollen',
      cardinality: ItemInterface::CARDINALITY_MULTIPLE
    ),
  ]
  #[TableReferencePropertyDefinition(
    tableReferenceName: 'roleMapping',
    property: 'rol_id',
  )]
  #[PropertyPreDefinedDefinition([
    [PreDefined::class, 'simpleInteger'],
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