<?php

namespace App\Item\ItemHandler_Validator;

use App\DaViEntity\EntityInterface;
use App\Item\ItemConfigurationInterface;
use App\Logger\Logger;
use App\Services\AppNamespaces;
use App\Services\Validation\ErrorCodes;
use Symfony\Component\Validator\Validator\TraceableValidator;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * validating with symfony validation constraints
 *
 * yaml configuration
 * <code>
 *  CommonValidatorItemHandler:
 *    not_null:
 *      logTitle: "NULL, 0 und negative Werte nicht erlaubt"
 *      logLevel: "warning"
 *      constraints:
 *        Positive: []
 *        NotNull: []
 * </code>
 *
 * <code>
 *    CommonValidatorItemHandler:
 *        no_email:
 *            logTitle: "E-Mail hier nicht erlaubt"
 *            logLevel: "warning"
 *            not_constraints:
 *              Email: []
 *  </code>
 *
 */
class CommonValidatorItemHandler extends AbstractValidatorItemHandler implements ValidatorItemHandlerInterface {

  private ValidatorInterface $validator;

  public function __construct(
    ValidatorInterface $validator,
    ErrorCodes $errorCodes,
    Logger $logger
  ) {
    parent::__construct($logger, $errorCodes);
    $this->validator = $validator;
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
    $itemConfiguration = $entity->getPropertyItem($property)
      ->getConfiguration();
    $settings = $itemConfiguration->getValidatorItemHandlerSettings(static::class);

    foreach ($settings as $key => $handlerSetting) {
      if (
        !$this->checkConstraints($handlerSetting, $value) ||
        !$this->checkConstraints($handlerSetting, $value, 'not_constraints')
      ) {
        $this->setItemValidationResultByCode($entity, $property, $handlerSetting['logCode'] ?? 'INT-2000');
      }
    }
  }

  private function checkConstraints($setting, $value, $constraintsCheck = 'constraints'): bool {
    if (!isset($setting[$constraintsCheck])) {
      return TRUE;
    }

    if ($this->validator instanceof TraceableValidator) {
      $this->validator->reset();
    }

    foreach ($setting[$constraintsCheck] as $constraint => $constraintSetting) {
      $constraint = $this->getConstraint($constraint, $constraintSetting);
      $errors = $this->validator->validate(
        $value,
        $constraint
      );
      if ($errors->count() && $constraintsCheck === 'constraints') {
        return FALSE;
      } elseif (!$errors->count() && $constraintsCheck === 'not_constraints') {
        return FALSE;
      }
    }
    return TRUE;
  }

  private function getConstraint($constraint, $constraintSetting) {
    $constraint = AppNamespaces::buildNamespace(AppNamespaces::SYMFONY_CONSTRAINTS, $constraint);
    return new $constraint($constraintSetting);
  }

  public function validateValueFromItemConfiguration(ItemConfigurationInterface $itemConfiguration, $value, string $client): bool {
    $settings = $itemConfiguration->getValidatorItemHandlerSettings($this::class);
    $label = $itemConfiguration->getLabel();

    foreach ($settings as $key => $handlerSetting) {
      if (
        !$this->checkConstraints($handlerSetting, $value) ||
        !$this->checkConstraints($handlerSetting, $value, 'not_constraints')
      ) {
        return FALSE;
      }
    }

    return TRUE;
  }

}
