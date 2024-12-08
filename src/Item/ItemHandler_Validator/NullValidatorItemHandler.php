<?php

namespace App\Item\ItemHandler_Validator;

use App\Item\ItemConfigurationInterface;
use App\DaViEntity\EntityInterface;

class NullValidatorItemHandler implements ValidatorItemHandlerInterface {

	public function validateValueFromItemConfiguration(ItemConfigurationInterface $itemConfiguration, $value, string $client): bool {
		return false;
	}

	public function validateItemFromGivenEntity(EntityInterface $entity, string $property): void {}
}
