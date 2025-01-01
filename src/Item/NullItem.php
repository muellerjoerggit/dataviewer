<?php

namespace App\Item;

use Generator;

class NullItem extends AbstractNullItem {

  protected ItemConfigurationInterface $itemConfiguration;

  public function __construct() {
    $this->itemConfiguration = new ItemConfiguration('NullItem');
  }

  public function getConfiguration(): ItemConfigurationInterface {
    return $this->itemConfiguration;
  }

}