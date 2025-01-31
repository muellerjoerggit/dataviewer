<?php

namespace App\DaViEntity\Schema;

use App\Database\BaseQuery\BaseQuery;
use App\DaViEntity\AdditionalData\AdditionalDataProvider;
use App\DaViEntity\EntityColumnBuilder\EntityColumnBuilder;
use App\DaViEntity\EntityCreator\EntityCreator;
use App\DaViEntity\EntityDataProvider\EntityDataProvider;
use App\DaViEntity\EntityListProvider\EntityListProvider;
use App\DaViEntity\EntityListSearch\EntityListSearch;
use App\DaViEntity\EntityRefiner\EntityRefiner;
use App\DaViEntity\EntityRepository\EntityRepositoryAttr;
use App\DaViEntity\Schema\Attribute\DatabaseAttr;
use App\DaViEntity\Schema\Attribute\EntityTypeAttr;
use App\Item\Property\PropertyAttributesContainer;

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

}