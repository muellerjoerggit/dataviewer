<?php

namespace App\EntityTypes\User;

use App\Database\BaseQuery\BaseQuery;
use App\Database\BaseQuery\CommonBaseQuery;
use App\Database\DaViDatabaseOne;
use App\Database\TableReferenceHandler\Attribute\CommonTableReferenceAttr;
use App\Database\TableReferenceHandler\CommonTableReferenceHandler;
use App\DaViEntity\AbstractEntity;
use App\DaViEntity\AdditionalData\AdditionalDataProvider;
use App\DaViEntity\AdditionalData\AdditionalDataProviderFromTableReferences;
use App\DaViEntity\ColumnBuilder\CommonColumnBuilder;
use App\DaViEntity\ColumnBuilder\ColumnBuilder;
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
use App\DaViEntity\Schema\Attribute\ExtendedEntityOverviewDefinitionSchemaAttr;
use App\DaViEntity\Traits\EntityPropertyTrait;
use App\EntityTypes\RoleUserMap\RoleUserMapEntity;
use App\Item\ItemInterface;
use App\Item\Property\Attribute\EntityOverviewPropertyAttr;
use App\Item\Property\Attribute\LabelPropertyAttr;
use App\Item\Property\Attribute\PropertyAttr;
use App\Item\Property\Attribute\PropertyPreDefinedAttr;
use App\Item\Property\Attribute\SearchPropertyDefinition;
use App\Item\Property\Attribute\UniquePropertyDefinition;
use App\Item\Property\PreDefinedAttributes\PreDefined;
use App\Services\EntityActionHandler\UrlActionDefinitionAttr;
use App\Services\EntityActionHandler\UrlActionHandler;

#[RepositoryDefinition(entityRepositoryClass: UserRepository::class)]
#[EntityTypeAttr(name: 'User', label: 'Benutzer')]
#[BaseQuery(baseQuery: CommonBaseQuery::class),
  SearchDefinition(entityListSearch: CommonSearch::class),
  DataProviderDefinition(dataProviderClass: CommonSqlDataProvider::class),
  CreatorDefinition(entityCreator: CommonCreator::class),
  RefinerDefinition(entityRefinerClass: CommonRefiner::class),
  ColumnBuilder(entityColumnBuilderClass: CommonColumnBuilder::class),
  ListProviderDefinition(entityListClass: CommonListProvider::class)
]
#[AdditionalDataProvider(additionalDataProviders: [AdditionalDataProviderFromTableReferences::class])]
#[DatabaseAttr(
  databaseClass: DaViDatabaseOne::class,
  baseTable: 'usr_data'),
]
#[CommonTableReferenceAttr(
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
  )]
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
  )]
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
  )]
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
  )]
  #[SearchPropertyDefinition
  ]
  #[PropertyPreDefinedAttr([
    [PreDefined::class, 'string'],
  ])]
  private $email;


  #[PropertyAttr(
    dataType: ItemInterface::DATA_TYPE_BOOL,
    label: 'Aktiv'
  )]
  #[EntityOverviewPropertyAttr(rank: 40)
  ]
  #[PropertyPreDefinedAttr([
    [PreDefined::class, 'integer'],
  ])]
  private $active;


  #[PropertyAttr(
    dataType: ItemInterface::DATA_TYPE_DATE_TIME,
    label: 'Inaktivierungsdatum'
  )]
  #[PropertyPreDefinedAttr([
    [PreDefined::class, 'dateTime'],
  ])]
  private $inactivation_date;


  #[PropertyAttr(
    dataType: ItemInterface::DATA_TYPE_INTEGER,
    label: 'Rollen',
    cardinality: ItemInterface::CARDINALITY_MULTIPLE
  )]
  #[PropertyPreDefinedAttr([
    [PreDefined::class, 'integer'],
  ])]
  private $roles;


  #[PropertyAttr(
    dataType: ItemInterface::DATA_TYPE_STRING,
    label: 'zweite E-Mail'
  )]
  #[PropertyPreDefinedAttr([
    [PreDefined::class, 'string'],
  ])]
  private $second_mail;

}