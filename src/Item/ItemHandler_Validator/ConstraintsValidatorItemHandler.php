<?php

namespace App\Item\ItemHandler_Validator;

use App\DaViEntity\EntityInterface;
use App\Item\ItemConfigurationInterface;
use App\Item\ItemHandler_Validator\Attribute\ConstraintsValidatorItemHandlerDefinition;
use App\Logger\Logger;
use App\Services\Validation\ErrorCodes;
use Symfony\Component\Validator\Validator\TraceableValidator;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * validating with symfony validation constraints
 */
class ConstraintsValidatorItemHandler extends AbstractValidatorItemHandler implements ValidatorItemHandlerInterface {

  public function __construct(
    private readonly ValidatorInterface $validator,
    ErrorCodes $errorCodes,
    Logger $logger
  ) {
    parent::__construct($logger, $errorCodes);
  }

  public function validateItemFromGivenEntity(EntityInterface $entity, string $property): void {
    if ($entity->hasPropertyItem($property)) {
      $item = $entity->getPropertyItem($property);
    } else {
      return;
    }

    foreach ($item->getValuesAsArray() as $value) {
      $this->validateValue($entity, $property, $value);
    }
  }

  protected function validateValue(EntityInterface $entity, string $property, mixed $value): void {
    $itemConfiguration = $entity->getPropertyItem($property)->getConfiguration();

    foreach ($itemConfiguration->iterateValidatorItemHandlerDefinitionsByClass(static::class) as $definition) {
      if(!$definition instanceof ConstraintsValidatorItemHandlerDefinition || !$definition->isValid()) {
        continue;
      }

      if (!$this->checkConstraint($definition, $value)) {
        $this->setItemValidationResultByCode($entity, $property, $definition->getLogCode());
      }
    }
  }

  private function checkConstraint(ConstraintsValidatorItemHandlerDefinition $definition, $value): bool {
    if ($this->validator instanceof TraceableValidator) {
      $this->validator->reset();
    }

    $class = $definition->getConstraintClass();
    if(class_exists($class)) {
      $constraint = new $class;
      $errors = $this->validator->validate(
        $value,
        $constraint
      );

      return $errors->count() && !$definition->isNegate();
    }

    return false;
  }

  public function validateValueFromItemConfiguration(ItemConfigurationInterface $itemConfiguration, $value, string $client): bool {
    foreach ($itemConfiguration->iterateValidatorItemHandlerDefinitionsByClass(static::class) as $definition) {
      if(!$definition instanceof ConstraintsValidatorItemHandlerDefinition || !$definition->isValid()) {
        continue;
      }

      if (!$this->checkConstraint($definition, $value)) {
        return false;
      }
    }

    return true;
  }

}
