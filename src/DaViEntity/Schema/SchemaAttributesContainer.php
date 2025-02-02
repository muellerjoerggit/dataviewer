<?php

namespace App\DaViEntity\Schema;

use App\Database\BaseQuery\BaseQuery;
use App\Database\TableReferenceHandler\Attribute\TableReferenceAttrInterface;
use App\DaViEntity\AdditionalData\AdditionalDataProvider;
use App\DaViEntity\ColumnBuilder\EntityColumnBuilder;
use App\DaViEntity\Creator\EntityCreator;
use App\DaViEntity\DataProvider\EntityDataProvider;
use App\DaViEntity\ListProvider\EntityListProvider;
use App\DaViEntity\ListSearch\EntityListSearch;
use App\DaViEntity\Refiner\EntityRefiner;
use App\DaViEntity\Repository\EntityRepositoryAttr;
use App\DaViEntity\Schema\Attribute\DatabaseAttr;
use App\DaViEntity\Schema\Attribute\EntityTypeAttr;
use App\Item\Property\PropertyAttributesContainer;
use App\Services\EntityAction\EntityActionConfigAttrInterface;
use Generator;

class SchemaAttributesContainer {

  private EntityTypeAttr $entityTypeAttr;
  private DatabaseAttr $databaseAttr;

  /**
   * @var EntityRepositoryAttr[]
   */
  private array $repositoryAttributes = [];

  /**
   * @var BaseQuery[]
   */
  private array $baseQueryAttributes = [];

  /**
   * @var EntityListSearch[]
   */
  private array $entityListSearchAttributes = [];

  /**
   * @var EntityDataProvider[]
   */
  private array $entityDataProviderAttributes = [];

  /**
   * @var EntityCreator[]
   */
  private array $entityCreatorAttributes = [];

  /**
   * @var EntityRefiner[]
   */
  private array $entityRefinerAttributes = [];

  /**
   * @var EntityColumnBuilder[]
   */
  private array $entityColumnBuilderAttributes = [];

  /**
   * @var EntityListProvider[]
   */
  private array $entityListProviderAttributes = [];

  /**
   * @var AdditionalDataProvider[]
   */
  private array $additionalDataProviderAttributes = [];

  /**
   * @var TableReferenceAttrInterface[]
   */
  private array $tableReferenceAttributes = [];

  /**
   * @var EntityActionConfigAttrInterface[]
   */
  private array $entityActionAttributes = [];

  /**
   * @var PropertyAttributesContainer[]
   */
  private array $properties = [];

  public function getEntityTypeAttr(): EntityTypeAttr | null {
    return $this->entityTypeAttr ?? null;
  }

  public function setEntityTypeAttr(EntityTypeAttr $entityTypeAttr): SchemaAttributesContainer {
    $this->entityTypeAttr = $entityTypeAttr;
    return $this;
  }

  public function getDatabaseAttr(): DatabaseAttr | null {
    return $this->databaseAttr ?? null;
  }

  public function setDatabaseAttr(DatabaseAttr $databaseAttr): SchemaAttributesContainer {
    $this->databaseAttr = $databaseAttr;
    return $this;
  }

  public function addRepositoryAttribute(EntityRepositoryAttr $repositoryAttr): SchemaAttributesContainer {
    $this->repositoryAttributes[] = $repositoryAttr;
    return $this;
  }

  public function addBaseQueryAttribute(BaseQuery $baseQuery): SchemaAttributesContainer {
    $this->baseQueryAttributes[] = $baseQuery;
    return $this;
  }

  public function addEntityListSearchAttribute(EntityListSearch $listSearch): SchemaAttributesContainer {
    $this->entityListSearchAttributes[] = $listSearch;
    return $this;
  }

  public function addDataProviderAttribute(EntityDataProvider $dataProvider): SchemaAttributesContainer {
    $this->entityDataProviderAttributes[] = $dataProvider;
    return $this;
  }

  public function addCreatorAttribute(EntityCreator $creator): SchemaAttributesContainer {
    $this->entityCreatorAttributes[] = $creator;
    return $this;
  }

  public function addRefinerAttribute(EntityRefiner $refiner): SchemaAttributesContainer {
    $this->entityRefinerAttributes[] = $refiner;
    return $this;
  }

  public function addColumnBuilderAttribute(EntityColumnBuilder $columnBuilder): SchemaAttributesContainer {
    $this->entityColumnBuilderAttributes[] = $columnBuilder;
    return $this;
  }

  public function addListProviderAttribute(EntityListProvider $listProvider): SchemaAttributesContainer {
    $this->entityListProviderAttributes[] = $listProvider;
    return $this;
  }

  public function addAdditionalDataProviderAttribute(AdditionalDataProvider $additionalDataProvider): SchemaAttributesContainer {
    $this->additionalDataProviderAttributes[] = $additionalDataProvider;
    return $this;
  }

  public function addTableReferenceAttribute(TableReferenceAttrInterface $tableReferenceAttr): SchemaAttributesContainer {
    $this->tableReferenceAttributes[] = $tableReferenceAttr;
    return $this;
  }

  /**
   * @return Generator<TableReferenceAttrInterface>
   */
  public function iterateTableReferenceAttributes(): Generator {
    foreach ($this->tableReferenceAttributes as $tableReferenceAttr) {
      yield $tableReferenceAttr;
    }
  }

  public function addEntityActionConfigAttribute(EntityActionConfigAttrInterface $entityActionAttribute): SchemaAttributesContainer {
    $this->entityActionAttributes[] = $entityActionAttribute;
    return $this;
  }

  /**
   * @return Generator<EntityActionConfigAttrInterface>
   */
  public function iterateEntityActionConfigAttributes(): Generator {
    foreach ($this->entityActionAttributes as $entityActionAttribute) {
      yield $entityActionAttribute;
    }
  }

  public function hasEntityActions(): bool {
    return !empty($this->entityActionAttributes);
  }

  public function addPropertyContainer(PropertyAttributesContainer $container, string $key): SchemaAttributesContainer {
    $this->properties[$key] = $container;
    return $this;
  }

}