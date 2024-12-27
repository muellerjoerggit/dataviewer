<?php

namespace App\DaViEntity;

use App\DaViEntity\AdditionalData\AdditionalDataProvider;
use App\DaViEntity\Attribute\BaseQuery;
use App\DaViEntity\Attribute\EntityCreator;
use App\DaViEntity\Attribute\EntityType;
use App\DaViEntity\EntityTypes\NullEntity\NullEntity;
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
      $arguments = $attribute->getArguments();
      if(isset($arguments[$key])) {
        return $arguments[$key];
      }
    }

    return $default;
  }

  public function getEntityType(string $classname): ?string {
    return $this->getAttributeKey($classname, EntityType::class, EntityType::NAME_PROPERTY, NullEntity::ENTITY_TYPE);
  }

  public function getBaseQueryClass(string $classname): ?string {
    return $this->getAttributeKey($classname, BaseQuery::class, BaseQuery::CLASS_PROPERTY, null);
  }

  public function getEntityCreatorClass(string $classname): ?string {
    return $this->getAttributeKey($classname, EntityCreator::class, EntityCreator::CLASS_PROPERTY, null);
  }

  public function getAdditionalDataProviderClassList(string $classname): array {
    return $this->getAttributeKey($classname, AdditionalDataProvider::class, AdditionalDataProvider::CLASS_PROPERTY, []);
  }

}