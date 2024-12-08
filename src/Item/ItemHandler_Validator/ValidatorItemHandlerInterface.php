<?php

namespace App\Item\ItemHandler_Validator;

use App\Item\ItemConfigurationInterface;
use App\DaViEntity\EntityInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * Validates items or values
 */
#[AutoconfigureTag('validator_item_handler')]
interface ValidatorItemHandlerInterface {

	/**
	 * validates an item/entity
	 */
	public function validateItemFromGivenEntity(EntityInterface $entity, string $property): void;

	/**
	 * validates a value by using item configuration
	 */
	public function validateValueFromItemConfiguration(ItemConfigurationInterface $itemConfiguration, $value, string $client): bool;

}
