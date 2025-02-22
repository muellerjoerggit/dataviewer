<?php

namespace App\Item\ItemHandler_Validator;

use App\DaViEntity\EntityInterface;
use App\Item\ItemHandler_Validator\Attribute\ValidatorItemHandlerDefinitionInterface;
use App\Item\ItemInterface;
use App\Logger\Logger;
use App\Logger\LogItems\DaViErrorLogItem;
use App\Logger\LogItems\LogItemInterface;
use App\Logger\LogItems\ValidationLogItem;
use App\Services\Validation\ErrorCodes;

abstract class AbstractValidatorItemHandler implements ValidatorItemHandlerInterface {

  public function __construct(
    protected readonly Logger $logger,
    protected readonly ErrorCodes $errorCodes)
  {}

  protected function getContext(EntityInterface $entity, string $property): array {
    if (!$entity->hasPropertyItem($property)) {
      $this->setItemValidationResultByCode($entity, $property, ErrorCodes::ERROR_MISSING_PROPERTY);
      return [NULL, NULL];
    }

    $item = $entity->getPropertyItem($property);
    $itemConfiguration = $item->getConfiguration();

    return [$item, $itemConfiguration];
  }

  protected function setItemValidationResultByCode(EntityInterface $entity, string $property, string $code, array $additionalPlaceholders = []): void {
    $this->errorCodes->logByCode($entity, $code, $additionalPlaceholders);
    $this->errorCodes->setItemError($entity, $property, $code);
  }

  protected function errorValidation(): void {
    $logItem = DaViErrorLogItem::createDaViErrorLogItem(
      'Error: Invalid validation result',
      'Invalid validation result',
      LogItemInterface::LOG_LEVEL_INTERNAL_ERROR
    );
    $this->logger->addLog($logItem);
  }

}
