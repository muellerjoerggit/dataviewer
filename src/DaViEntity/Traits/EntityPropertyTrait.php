<?php

namespace App\DaViEntity\Traits;

use App\DaViEntity\EntityInterface;
use App\Item\ItemInterface;
use App\Item\Property\PropertyItemInterface;
use App\Item\Property\PropertyItems\NullPropertyItem;

trait EntityPropertyTrait {

  public function getPropertyRawValues(string $property): mixed {
    if ($this->schema->hasProperty($property)) {
      $value = $this->{$property};
    } else {
      return NULL;
    }

    if ($value instanceof ItemInterface) {
      $value = $value->getRawValues();
    }

    return $value;
  }

  public function getPropertyValues(string $property): mixed {
    if ($this->schema->hasProperty($property)) {
      $value = $this->{$property};
    } else {
      return NULL;
    }

    if ($value instanceof ItemInterface) {
      $value = $value->getCastValues();
    }

    return $value;
  }

  public function getPropertyValueAsString(string $property): string {
    return $this->getPropertyItem($property)->getValuesAsString();
  }

  public function getPropertyItem(string $property): PropertyItemInterface {
    if ($this->hasPropertyItem($property)) {
      return $this->{$property};
    } else {
      return NullPropertyItem::create();
    }
  }

  public function hasPropertyItem(string $property): bool {
    return $this->schema->hasProperty($property) && property_exists($this, $property) && isset($this->{$property});
  }

  public function setPropertyItem(string $property, ItemInterface $item): EntityInterface {
    if ($this->schema->hasProperty($property) && property_exists($this, $property)) {
      $this->{$property} = $item;
    }

    return $this;
  }

  public function getMultiplePropertyItems(array $properties): array {
    $propertyItems = [];
    foreach ($properties as $property) {
      $propertyItems[$property] = $this->getPropertyItem($property);
    }

    return $propertyItems;
  }

}
