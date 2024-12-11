<?php

namespace App\Item\ItemHandler_Validator;

use App\DaViEntity\EntityInterface;
use App\Item\ItemInterface;
use App\Logger\Logger;
use App\Logger\LogItems\DaViErrorLogItem;
use App\Logger\LogItems\LogItemInterface;
use App\Logger\LogItems\ValidationLogItem;
use App\Services\Validation\ErrorCodes;

abstract class AbstractValidatorItemHandler implements ValidatorItemHandlerInterface {

  protected Logger $logger;

  protected ErrorCodes $errorCodes;

  public function __construct(Logger $logger, ErrorCodes $errorCodes) {
    $this->logger = $logger;
    $this->errorCodes = $errorCodes;
  }

  protected function getContext(EntityInterface $entity, string $property): array {
    if (!$entity->hasPropertyItem($property)) {
      $this->setItemValidationResultByCode($entity, $property, ErrorCodes::ERROR_MISSING_PROPERTY);
      return [NULL, NULL, []];
    }

    $item = $entity->getPropertyItem($property);
    $itemConfiguration = $item->getConfiguration();
    $handlerSettings = $itemConfiguration->getValidatorItemHandlerSettings(static::class);

    return [$item, $itemConfiguration, $handlerSettings];
  }

  protected function setItemValidationResultByCode(EntityInterface $entity, string $property, string $code, array $additionalPlaceholders = []): void {
    $this->errorCodes->logByCode($entity, $code, $additionalPlaceholders);
    $this->errorCodes->setItemError($entity, $property, $code);
  }

  protected function createResultMessage(array $handlerSetting, string $label): array {
    if (!empty($handlerSetting['logMessage']) && !empty($label)) {
      $logMessage = 'Feld "' . $label . '": ' . $handlerSetting['logMessage'];
    } elseif (!empty($handlerSetting['logMessage']) && empty($label)) {
      $logMessage = $handlerSetting['logMessage'];
    } elseif (!isset($handlerSetting['logMessage']) && !empty($label)) {
      $logMessage = 'Feld "' . $label;
    } else {
      $logMessage = '';
    }

    $errorCode = $handlerSetting['errorCode'] ?? '';

    return [
      'result' => FALSE,
      'logLevel' => $handlerSetting['logLevel'] ?? LogItemInterface::LOG_LEVEL_INFO,
      'logTitle' => $handlerSetting['logTitle'] ?? 'Validation',
      'logMessage' => $logMessage,
      'logCode' => $errorCode,
    ];
  }

  protected function setItemValidationResult(array $validationResult, ?ItemInterface $item = NULL, ?EntityInterface $entity = NULL): void {
    $logItem = ValidationLogItem::createValidationLogItem(
      $validationResult['logMessage'] ?? 'Failed validation',
      $validationResult['logLevel'] ?? LogItemInterface::LOG_LEVEL_NOTICE
    );
    if ($item instanceof ItemInterface) {
      if (in_array($validationResult['logLevel'], LogItemInterface::RED_LOG_LEVELS)) {
        $item->setRedError(TRUE);
      }
      if (in_array($validationResult['logLevel'], LogItemInterface::YELLOW_LOG_LEVELS)) {
        $item->setYellowError(TRUE);
      }
    }

    $this->logger->addLog($logItem);

    if ($entity instanceof EntityInterface) {
      $entity->addLogs($logItem);
    }
  }

  protected function errorValidation(): void {
    $logItem = DaViErrorLogItem::createDaViErrorLogItem(
      'Error: Invalid validation result',
      'Invalid validation result',
      LogItemInterface::LOG_LEVEL_DAVI_ERROR
    );
    $this->logger->addLog($logItem);
  }

}
