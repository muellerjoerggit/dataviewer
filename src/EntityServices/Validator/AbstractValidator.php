<?php

namespace App\EntityServices\Validator;

use App\DaViEntity\EntityInterface;
use App\Item\ItemHandler_Validator\ValidatorItemHandlerLocator;
use App\Logger\LogItems\LogItemInterface;
use App\Logger\LogItems\ValidationLogItem;
use App\Services\Validation\ErrorCodes;

class AbstractValidator implements ValidatorInterface {

  public function __construct(
    protected readonly ValidatorItemHandlerLocator $validatorHandlerLocator,
    protected readonly ErrorCodes $errorCodes,
  ) {}

  public function validateEntity(EntityInterface $entity): void {
    $this->validateProperties($entity);
  }

  protected function logByCode(EntityInterface $entity, string $code): void {
    $error = $this->errorCodes->buildError($entity, $code);
    $logItem = ValidationLogItem::createValidationLogItem($error['message'], $error['level'], $code);
    $entity->addLogs($logItem);
  }

  protected function setItemError(EntityInterface $entity, string $property, string $code): void {
    $level = $this->errorCodes->getErrorLevel($code);
    $item = $entity->getPropertyItem($property);

    if (in_array($level, LogItemInterface::RED_LOG_LEVELS)) {
      $item->setRedError(TRUE);
    } elseif (in_array($level, LogItemInterface::YELLOW_LOG_LEVELS)) {
      $item->setYellowError(TRUE);
    }
  }

  protected function logItemAndEntity(EntityInterface $entity, string $code, string $property): void {
    $this->logByCode($entity, $code);
    $this->setItemError($entity, $property, $code);
  }

  protected function validateProperties(EntityInterface $entity): void {
    $schema = $entity->getSchema();

    foreach ($schema->iterateProperties() as $property => $config) {
      if ($entity->hasPropertyItem($property)) {
        $item = $entity->getPropertyItem($property);
        $itemConfiguration = $item->getConfiguration();
      } else {
        continue;
      }

      if ($itemConfiguration->hasValidatorHandlerDefinition()) {
        foreach ($this->validatorHandlerLocator->getValidatorHandlerFromItem($itemConfiguration) as $handler) {
          $handler->validateItemFromGivenEntity($entity, $property);
        }
      }
    }
  }

}