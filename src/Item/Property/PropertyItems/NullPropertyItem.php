<?php

namespace App\Item\Property\PropertyItems;

use App\Item\NullItem;
use App\Item\Property\PropertyConfiguration;
use App\Item\Property\PropertyItemInterface;

class NullPropertyItem extends NullItem implements PropertyItemInterface {

  public function __construct() {
    $this->itemConfiguration = new PropertyConfiguration('NullItem');
  }

}