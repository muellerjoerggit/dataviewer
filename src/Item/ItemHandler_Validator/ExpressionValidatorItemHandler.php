<?php

namespace App\Item\ItemHandler_Validator;

use App\DaViEntity\EntityInterface;
use App\Item\ItemConfigurationInterface;
use App\Item\ItemHandler_Validator\Attribute\ExpressionValidatorItemHandlerDefinition;
use App\Logger\Logger;
use App\Services\Validation\ErrorCodes;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * validation with help of symfony expression language
 */
class ExpressionValidatorItemHandler extends AbstractValidatorItemHandler implements ValidatorItemHandlerInterface {

  public function validateItemFromGivenEntity(EntityInterface $entity, string $property): void {
    if ($entity->hasPropertyItem($property)) {
      $item = $entity->getPropertyItem($property);
      $itemConfiguration = $item->getConfiguration();
    } else {
      return;
    }

    foreach ($item->getValuesAsArray() as $value) {
      foreach ($itemConfiguration->iterateValidatorItemHandlerDefinitionsByClass(static::class) as $definition) {
        if(!$definition instanceof ExpressionValidatorItemHandlerDefinition) {
          continue;
        }

        if(!$this->checkExpression($definition, $value)) {
          $this->setItemValidationResultByCode($entity, $property, $definition->getLogCode());
        }
      }
    }
  }

  public function validateValueFromItemConfiguration(ItemConfigurationInterface $itemConfiguration, $value, string $client): bool {
    foreach ($itemConfiguration->iterateValidatorItemHandlerDefinitionsByClass(static::class) as $definition) {
      if(!$definition instanceof ExpressionValidatorItemHandlerDefinition) {
        continue;
      }

      if(!$this->checkExpression($definition, $value)) {
        return false;
      }
    }

    return true;
  }

  private function checkExpression(ExpressionValidatorItemHandlerDefinition $definition, mixed $value): bool {
    $expressionLanguage = new ExpressionLanguage();
    if (!is_array($value)) {
      $value = ['value' => $value];
    }

    return !$definition->isNegate() && $expressionLanguage->evaluate($definition->getExpression(), $value);
  }

}
