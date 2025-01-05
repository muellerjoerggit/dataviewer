<?php

namespace App\Item\Property;

use App\Item\ItemConfigurationInterface;
use App\Item\Property\PropertyItems\NullPropertyItem;
use App\Item\Property\PropertyItems\PropertyItem;
use Throwable;

class PropertyBuilder {

  public function createProperty(PropertyConfiguration $configuration, $values): PropertyItemInterface {
    try {
      $property = new PropertyItem($configuration);
      $property->setRawValues($values);
      return $property;
    } catch (Throwable $ex) {
      return NullPropertyItem::create();
    }
  }

}