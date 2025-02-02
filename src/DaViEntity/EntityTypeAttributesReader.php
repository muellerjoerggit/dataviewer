<?php

namespace App\DaViEntity;

use App\Database\BaseQuery\BaseQuery;
use App\Database\TableReferenceHandler\Attribute\TableReferenceAttrInterface;
use App\DaViEntity\AdditionalData\AdditionalDataProvider;
use App\DaViEntity\ColumnBuilder\EntityColumnBuilder;
use App\DaViEntity\Creator\EntityCreator;
use App\DaViEntity\DataProvider\EntityDataProvider;
use App\DaViEntity\EntityLabel\EntityLabelCrafter;
use App\DaViEntity\ListProvider\EntityListProvider;
use App\DaViEntity\ListSearch\EntityListSearch;
use App\DaViEntity\Refiner\EntityRefiner;
use App\DaViEntity\Repository\EntityRepositoryAttr;
use App\DaViEntity\Schema\Attribute\DatabaseAttr;
use App\DaViEntity\Schema\Attribute\EntityTypeAttr;
use App\DaViEntity\Schema\SchemaAttributesContainer;
use App\EntityTypes\NullEntity\NullEntity;
use App\Services\AbstractAttributesReader;
use App\Services\EntityAction\EntityActionConfigAttrInterface;
use ReflectionAttribute;

class EntityTypeAttributesReader extends AbstractAttributesReader {

  public function buildSchemaAttributesContainer(string $entityClass): SchemaAttributesContainer {
    $container = new SchemaAttributesContainer();
    $reflection = $this->reflectClass($entityClass);

    if(!$reflection) {
      return $container;
    }

    foreach($reflection->getAttributes() as $attribute) {
      $instance = $attribute->newInstance();
      $this->addAttributeInstanceToContainer($container, $instance);
    }

    return $container;
  }

  private function addAttributeInstanceToContainer(SchemaAttributesContainer $container, mixed $attribute): void {
    if(!is_object($attribute)) {
      return;
    }

    if($attribute instanceof TableReferenceAttrInterface) {
      $container->addTableReferenceAttribute($attribute);
      return;
    } elseif ($attribute instanceof EntityActionConfigAttrInterface) {
      $container->addEntityActionConfigAttribute($attribute);
      return;
    }

    switch(get_class($attribute)) {
      case EntityTypeAttr::class:
        $container->setEntityTypeAttr($attribute);
        break;
      case DatabaseAttr::class:
        $container->setDatabaseAttr($attribute);
        break;
      case EntityRepositoryAttr::class:
        $container->addRepositoryAttribute($attribute);
        break;
      case BaseQuery::class:
        $container->addBaseQueryAttribute($attribute);
        break;
      case EntityListSearch::class:
        $container->addEntityListSearchAttribute($attribute);
        break;
      case EntityDataProvider::class:
        $container->addDataProviderAttribute($attribute);
        break;
      case EntityCreator::class:
        $container->addCreatorAttribute($attribute);
        break;
      case EntityRefiner::class:
        $container->addRefinerAttribute($attribute);
        break;
      case EntityColumnBuilder::class:
        $container->addColumnBuilderAttribute($attribute);
        break;
      case EntityListProvider::class:
        $container->addListProviderAttribute($attribute);
        break;
      case AdditionalDataProvider::class:
        $container->addAdditionalDataProviderAttribute($attribute);
        break;
    }
  }

  private function getAttributeKey(string $classname, string $attributeClass, string $key, mixed $default): mixed {
    $reflection = $this->reflectClass($classname);

    if($reflection) {
      $attributes = $reflection->getAttributes($attributeClass);
      $attribute = reset($attributes);

      if(!$attribute instanceof ReflectionAttribute) {
        return $default;
      }

      $arguments = $attribute->getArguments();
      if(isset($arguments[$key])) {
        return $arguments[$key];
      }
    }

    return $default;
  }

  public function getEntityType(string | EntityInterface $classname): ?string {
    $classname = $this->resolveEntityClass($classname);
    return $this->getAttributeKey($classname, EntityTypeAttr::class, EntityTypeAttr::NAME_PROPERTY, NullEntity::ENTITY_TYPE);
  }

  public function getBaseQueryClass(string | EntityInterface $classname): string {
    $classname = $this->resolveEntityClass($classname);
    return $this->getAttributeKey($classname, BaseQuery::class, BaseQuery::CLASS_PROPERTY, '');
  }

  public function getEntityCreatorClass(string | EntityInterface $classname): string {
    $classname = $this->resolveEntityClass($classname);
    return $this->getAttributeKey($classname, EntityCreator::class, EntityCreator::CLASS_PROPERTY, '');
  }

  public function getAdditionalDataProviderClassList(string | EntityInterface $classname): array {
    $classname = $this->resolveEntityClass($classname);
    return $this->getAttributeKey($classname, AdditionalDataProvider::class, AdditionalDataProvider::CLASS_PROPERTY, []);
  }

  public function getEntityListSearchClass(string | EntityInterface $classname): string {
    $classname = $this->resolveEntityClass($classname);
    return $this->getAttributeKey($classname, EntityListSearch::class, EntityListSearch::CLASS_PROPERTY, '');
  }

  public function getEntityListProviderClass(string | EntityInterface $classname): string {
    $classname = $this->resolveEntityClass($classname);
    return $this->getAttributeKey($classname, EntityListProvider::class, EntityListProvider::CLASS_PROPERTY, '');
  }

  public function getEntityColumnBuilderClass(string | EntityInterface $classname): string {
    $classname = $this->resolveEntityClass($classname);
    return $this->getAttributeKey($classname, EntityColumnBuilder::class, EntityColumnBuilder::CLASS_PROPERTY, '');
  }

  public function getEntityLabelCrafterClass(string | EntityInterface $classname): string {
    $classname = $this->resolveEntityClass($classname);
    return $this->getAttributeKey($classname, EntityLabelCrafter::class, EntityLabelCrafter::CLASS_PROPERTY, '');
  }

  public function getRepositoryClass(string | EntityInterface $classname): string {
    $classname = $this->resolveEntityClass($classname);
    return $this->getAttributeKey($classname, EntityRepositoryAttr::class, EntityRepositoryAttr::CLASS_PROPERTY, '');
  }

  public function getEntityDataProviderClass(string | EntityInterface $classname): string {
    $classname = $this->resolveEntityClass($classname);
    return $this->getAttributeKey($classname, EntityDataProvider::class, EntityDataProvider::CLASS_PROPERTY, '');
  }

  public function getEntityRefinerClass(string | EntityInterface $classname): string {
    $classname = $this->resolveEntityClass($classname);
    return $this->getAttributeKey($classname, EntityRefiner::class, EntityRefiner::CLASS_PROPERTY, '');
  }

  private function resolveEntityClass(string | EntityInterface $classname): string {
    if($classname instanceof EntityInterface) {
      return get_class($classname);
    }

    return $classname;
  }

}