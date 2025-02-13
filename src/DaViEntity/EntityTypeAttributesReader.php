<?php

namespace App\DaViEntity;

use App\Database\BaseQuery\BaseQuery;
use App\Database\SqlFilter\SqlFilterInterface;
use App\Database\SqlFilterHandler\Attribute\SqlFilterDefinitionInterface;
use App\Database\TableReferenceHandler\Attribute\TableReferenceAttrInterface;
use App\DaViEntity\AdditionalData\AdditionalDataProvider;
use App\DaViEntity\ColumnBuilder\ColumnBuilderDefinition;
use App\DaViEntity\Creator\CreatorDefinition;
use App\DaViEntity\DataProvider\DataProviderDefinition;
use App\DaViEntity\EntityLabel\LabelCrafter;
use App\DaViEntity\ListProvider\ListProviderDefinition;
use App\DaViEntity\Search\SearchDefinition;
use App\DaViEntity\Refiner\RefinerDefinition;
use App\DaViEntity\Repository\RepositoryDefinition;
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
    } elseif ($attribute instanceof SqlFilterDefinitionInterface && $attribute->isValid()) {
      $container->addSqlFilterDefinitionsAttribute($attribute);
    }

    switch(get_class($attribute)) {
      case EntityTypeAttr::class:
        $container->setEntityTypeAttr($attribute);
        break;
      case DatabaseAttr::class:
        $container->setDatabaseAttr($attribute);
        break;
      case RepositoryDefinition::class:
        $container->addRepositoryAttribute($attribute);
        break;
      case BaseQuery::class:
        $container->addBaseQueryAttribute($attribute);
        break;
      case SearchDefinition::class:
        $container->addEntityListSearchAttribute($attribute);
        break;
      case DataProviderDefinition::class:
        $container->addDataProviderAttribute($attribute);
        break;
      case CreatorDefinition::class:
        $container->addCreatorAttribute($attribute);
        break;
      case RefinerDefinition::class:
        $container->addRefinerAttribute($attribute);
        break;
      case ColumnBuilderDefinition::class:
        $container->addColumnBuilderAttribute($attribute);
        break;
      case ListProviderDefinition::class:
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

  public function getEntityListSearchClass(string | EntityInterface $classname): string {
    $classname = $this->resolveEntityClass($classname);
    return $this->getAttributeKey($classname, SearchDefinition::class, SearchDefinition::CLASS_PROPERTY, '');
  }

  public function getEntityListProviderClass(string | EntityInterface $classname): string {
    $classname = $this->resolveEntityClass($classname);
    return $this->getAttributeKey($classname, ListProviderDefinition::class, ListProviderDefinition::CLASS_PROPERTY, '');
  }

  public function getEntityLabelCrafterClass(string | EntityInterface $classname): string {
    $classname = $this->resolveEntityClass($classname);
    return $this->getAttributeKey($classname, LabelCrafter::class, LabelCrafter::CLASS_PROPERTY, '');
  }

  public function getRepositoryClass(string | EntityInterface $classname): string {
    $classname = $this->resolveEntityClass($classname);
    return $this->getAttributeKey($classname, RepositoryDefinition::class, RepositoryDefinition::CLASS_PROPERTY, '');
  }

  public function getEntityDataProviderClass(string | EntityInterface $classname): string {
    $classname = $this->resolveEntityClass($classname);
    return $this->getAttributeKey($classname, DataProviderDefinition::class, DataProviderDefinition::CLASS_PROPERTY, '');
  }

  public function getEntityRefinerClass(string | EntityInterface $classname): string {
    $classname = $this->resolveEntityClass($classname);
    return $this->getAttributeKey($classname, RefinerDefinition::class, RefinerDefinition::CLASS_PROPERTY, '');
  }

  private function resolveEntityClass(string | EntityInterface $classname): string {
    if($classname instanceof EntityInterface) {
      return get_class($classname);
    }

    return $classname;
  }

}