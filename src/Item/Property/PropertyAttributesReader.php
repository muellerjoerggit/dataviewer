<?php

namespace App\Item\Property;

use App\DaViEntity\Schema\SchemaAttributesContainer;
use App\Item\Property\Attribute\EntityOverviewPropertyAttr;
use App\Item\Property\Attribute\ExtendedEntityOverviewPropertyAttr;
use App\Item\Property\Attribute\LabelPropertyAttr;
use App\Item\Property\Attribute\PropertyAttr;
use App\Item\Property\Attribute\SearchPropertyAttr;
use App\Item\Property\Attribute\UniquePropertyAttr;
use App\Services\AbstractAttributesReader;
use ReflectionAttribute;
use ReflectionProperty;

class PropertyAttributesReader extends AbstractAttributesReader {

  public function appendPropertyAttributesContainer(SchemaAttributesContainer $container, string $entityClass): bool {
    $reflection = $this->reflectClass($entityClass);

    if(!$reflection) {
      return false;
    }

    foreach ($reflection->getProperties() as $property) {
      $this->processProperty($property, $container);
    }

    return true;
  }

  private function processProperty(ReflectionProperty $property, SchemaAttributesContainer $container): void {
    $propertyContainer = new PropertyAttributesContainer();

    foreach ($property->getAttributes() as $attribute) {
      $this->processAttribute($propertyContainer, $property, $attribute);
      $container->addPropertyContainer($propertyContainer, $property->getName());
    }
  }

  private function processAttribute(PropertyAttributesContainer $container, ReflectionProperty $property, ReflectionAttribute $attribute): void {
    $instance = $attribute->newInstance();

    if($instance instanceof PropertyAttr) {
      $instance->setProperty($property->getName());
      $container->setPropertyAttr($instance);
    } elseif ($instance instanceof LabelPropertyAttr) {
      $instance->setProperty($property->getName());
      $container->setLabelPropertyAttr($instance);
    } elseif ($instance instanceof SearchPropertyAttr) {
      $instance->setProperty($property->getName());
      $container->setSearchPropertyAttr($instance);
    } elseif ($instance instanceof UniquePropertyAttr) {
      $instance->setProperty($property->getName());
      $container->setUniquePropertyAttr($instance);
    } elseif ($instance instanceof EntityOverviewPropertyAttr) {
      $instance->setProperty($property->getName());
      $container->setEntityOverviewPropertyAttr($instance);
    } elseif ($instance instanceof ExtendedEntityOverviewPropertyAttr) {
      $instance->setProperty($property->getName());
      $container->setExtendedEntityOverviewPropertyAttr($instance);
    }
  }

}