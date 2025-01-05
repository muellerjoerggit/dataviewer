<?php

namespace App\DaViEntity;

use App\Database\BaseQuery\BaseQuery;
use App\DaViEntity\AdditionalData\AdditionalDataProvider;
use App\DaViEntity\Attribute\EntityType;
use App\DaViEntity\EntityColumnBuilder\EntityColumnBuilder;
use App\DaViEntity\EntityCreator\EntityCreator;
use App\DaViEntity\EntityDataProvider\EntityDataProvider;
use App\DaViEntity\EntityLabel\EntityLabelCrafter;
use App\DaViEntity\EntityListProvider\EntityListProvider;
use App\DaViEntity\EntityListSearch\EntityListSearch;
use App\DaViEntity\EntityRefiner\EntityRefiner;
use App\DaViEntity\EntityRepository\EntityRepository;
use App\DaViEntity\EntityTypes\NullEntity\NullEntity;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;

class EntityTypeAttributesReader {

  private function reflectClass(string $classname): ?ReflectionClass {
    try {
      $reflection = new ReflectionClass($classname);
    } catch (ReflectionException $e) {
      return null;
    }
    return $reflection;
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
    return $this->getAttributeKey($classname, EntityType::class, EntityType::NAME_PROPERTY, NullEntity::ENTITY_TYPE);
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
    return $this->getAttributeKey($classname, EntityRepository::class, EntityRepository::CLASS_PROPERTY, '');
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