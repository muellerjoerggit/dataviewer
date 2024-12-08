<?php

namespace App\Item\Property;

use App\Item\ItemConfigurationInterface;
use App\Item\Property\PropertyItems\NullPropertyItem;
use App\Item\Property\PropertyItems\PropertyItem;

class PropertyBuilder {

  public function createProperty(ItemConfigurationInterface $configuration, $values): PropertyItemInterface {
    try {
      $property = new PropertyItem($configuration);
      $property->setRawValues($values);
      return $property;
    } catch (\Throwable $ex) {
      return $this->createNullItem();
    }
  }

  public function createNullItem(): PropertyItemInterface {
    return new NullPropertyItem();
  }

}