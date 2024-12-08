<?php

namespace App\Item\ItemHandler_AdditionalData;

use App\DataCollections\TableData;
use App\DaViEntity\EntityInterface;

class NullAdditionalDataItemHandler implements AdditionalDataItemHandlerInterface {

  public function getValues(EntityInterface $entity, string $property): TableData|array {
    return [];
  }

}
