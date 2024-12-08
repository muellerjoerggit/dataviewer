<?php

namespace App\Item\ItemHandler_Validator;

use App\Item\ItemConfigurationInterface;
use App\Logger\Logger;
use App\Services\Validation\ErrorCodes;
use App\DaViEntity\EntityInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * validation with help of symfony expression language
 *
 * yaml configuration
 * <code>
 * 	ExpressionValidatorItemHandler:
 * 		not_null:
 * 			logTitle: "NULL nicht erlaubt"
 * 			logLevel: "warning"
 * 			expression: "value not null"
 * </code>
 *
 */
class ExpressionValidatorItemHandler extends AbstractValidatorItemHandler implements ValidatorItemHandlerInterface {

	public function __construct(Logger $logger, ErrorCodes $errorCodes) {
		parent::__construct($logger, $errorCodes);
	}

	public function validateItemFromGivenEntity(EntityInterface $entity, string $property): void {
		if($entity->hasPropertyItem($property)) {
			$item = $entity->getPropertyItem($property);
			$itemConfiguration = $item->getConfiguration();
		} else {
			return;
		}

		foreach ($item->getValuesAsArray() as $value) {
			$validationResult = $this->validateValueFromItemConfiguration($itemConfiguration, $value, $entity->getClient());
			if(isset($validationResult['result']) && !$validationResult['result']) {
				$this->setItemValidationResult($validationResult, $item, $entity);
			} elseif (!isset($validationResult['result'])) {
				$this->errorValidation();
			}
		}

	}

	public function validateValueFromItemConfiguration(ItemConfigurationInterface $itemConfiguration, $value, string $client): bool {
		$settings = $itemConfiguration->getValidatorItemHandlerSettings($this::class);
		$label = $itemConfiguration->getLabel();

		foreach ($settings as $handlerSetting) {
			$check = $this->checkExpression($handlerSetting, $value);
			$negation = $handlerSetting['negation'] ?? false;

			if(!$check && !$negation) {
				return false;
			}

		}

		return true;
	}

	private function checkExpression(array $setting, mixed $value): bool {
		$expressionLanguage = new ExpressionLanguage();
		$result = true;
		if(!is_array($value)) {
			$value = ['value' => $value];
		}

		if(isset($setting['expression'])) {
			$expression = $setting['expression'];
			$result = $expressionLanguage->evaluate($expression, $value);
		}

		return $result;
	}

}
