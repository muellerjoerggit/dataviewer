<?php

namespace App\Item\Property;

use App\Item\ItemInterface;

interface PropertyItemInterface extends ItemInterface {

  public function getConfiguration(): PropertyConfiguration;

}