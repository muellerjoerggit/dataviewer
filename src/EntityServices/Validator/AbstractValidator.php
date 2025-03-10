<?php

namespace App\EntityServices\Validator;

use App\DaViEntity\EntityInterface;
use App\EntityServices\Traits\EntityLogTrait;
use App\Item\ItemHandler_Validator\ValidatorItemHandlerLocator;
use App\Logger\LogItems\LogItemInterface;
use App\Logger\LogItems\ValidationLogItem;
use App\Services\Validation\ErrorCodes;

class AbstractValidator implements ValidatorInterface {

  use EntityLogTrait;

  public function __construct(
    protected readonly ValidatorItemHandlerLocator $validatorHandlerLocator,
    protected readonly ErrorCodes $errorCodes,
  ) {}

  public function validateEntity(EntityInterface $entity): void {
    $this->validateProperties($entity);
  }

  protected function logByCode(EntityInterface $entity, string $code): void {
    $entity->addLogs($this->createValidationLogItemByCode($this->errorCodes, $entity, $code));
  }

  protected function logItemAndEntity(EntityInterface $entity, string $code, string $property): void {
    $this->logByCode($entity, $code);
    $this->setItemErrorByCode($this->errorCodes, $entity, $property, $code);
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