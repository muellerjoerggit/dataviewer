<?php

namespace App\Item\Property\PropertyItems;

use App\Item\AbstractItem;
use App\Item\ItemConfigurationInterface;
use App\Item\Property\PropertyConfiguration;
use App\Item\Property\PropertyItemInterface;

class PropertyItem extends AbstractItem implements PropertyItemInterface {

  public function __construct(
    protected PropertyConfiguration $configuration
  ) {}

  public function getConfiguration(): PropertyConfiguration {
    return $this->configuration;
  }

}