<?php

namespace App\Item\ItemHandler_Validator;

use App\Item\ItemConfigurationInterface;
use App\DaViEntity\EntityInterface;

/**
 * checks, whether
 *
 * yaml config example:
 * <code>
 * OptionsValidatorItemHandler:
 *   logCode: "INT-2000"
 * </code>
 *
 */
class OptionsValidatorItemHandler extends AbstractValidatorItemHandler {

	public const ERROR_CODE_UNKNOWN_OPTION = 'ALL-3000';

    public function validateItemFromGivenEntity(EntityInterface $entity, string $property): void {
		[$item, $itemConfiguration, $handlerSetting] = $this->getContext($entity, $property);

		if(!$item) {
			return;
		}

		$errorCode = $handlerSetting['logCode'] ?? self::ERROR_CODE_UNKNOWN_OPTION;
		$options = $itemConfiguration->getSetting('options', []);
		$emptyAllowed = $options['emptyAllowed'] ?? false;

		if(!$emptyAllowed && $item->isValuesNull()) {
			$this->setItemValidationResultByCode($entity, $property, $errorCode);
			return;
		}

		foreach ($item->iterateValues() as $value) {
			if(!array_key_exists($value, $options)) {
				$this->setItemValidationResultByCode($entity, $property, $errorCode, ['option' => $value]);
			}
		}
    }

  public function validateValueFromItemConfiguration(ItemConfigurationInterface $itemConfiguration, $value, string $client): bool {
		$options = $itemConfiguration->getSetting('options', []);

		if(!array_key_exists($value, $options)) {
			return false;
		}

    return true;
  }
}
