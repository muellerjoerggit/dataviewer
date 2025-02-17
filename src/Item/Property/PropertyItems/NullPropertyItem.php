<?php

namespace App\Item\Property\PropertyItems;

use App\Item\AbstractNullItem;
use App\Item\Property\PropertyConfiguration;
use App\Item\Property\PropertyItemInterface;

class NullPropertyItem extends AbstractNullItem implements PropertyItemInterface {

  protected PropertyConfiguration $itemConfiguration;

  public function __construct() {
    $this->itemConfiguration = PropertyConfiguration::createNull();
  }

  public function getConfiguration(): PropertyConfiguration {
    return $this->itemConfiguration;
  }

}