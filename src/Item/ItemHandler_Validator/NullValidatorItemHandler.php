<?php

namespace App\Item\ItemHandler_Validator;

use App\DaViEntity\EntityInterface;
use App\Item\ItemConfigurationInterface;

class NullValidatorItemHandler implements ValidatorItemHandlerInterface {

  public function validateValueFromItemConfiguration(ItemConfigurationInterface $itemConfiguration, $value, string $client): bool {
    return FALSE;
  }

  public function validateItemFromGivenEntity(EntityInterface $entity, string $property): void {}

}
