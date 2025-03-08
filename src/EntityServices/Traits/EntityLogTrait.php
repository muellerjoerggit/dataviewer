<?php

namespace App\EntityServices\Traits;

use App\DaViEntity\EntityInterface;
use App\Item\ItemInterface;
use App\Logger\LogItems\LogItem;
use App\Logger\LogItems\LogItemInterface;
use App\Logger\LogItems\ValidationLogItem;
use App\Services\Validation\ErrorCodes;

trait EntityLogTrait {

  public function createLogItemByCode(ErrorCodes $errorCodes, EntityInterface $entity, string $code, string $title = '', $dateTime = NULL, array $rawLogs = []): LogItemInterface {
    $error = $errorCodes->buildError($entity, $code);
    return LogItem::createLogItem($error['message'], $title, $error['level'], $dateTime, $rawLogs);
  }

  public function createValidationLogItemByCode(ErrorCodes $errorCodes, EntityInterface $entity, string $code): LogItemInterface {
    $error = $errorCodes->buildError($entity, $code);
    return ValidationLogItem::createValidationLogItem($error['message'], $error['level'], $code);
  }

  protected function setItemErrorByCode(ErrorCodes $errorCodes, EntityInterface $entity, string $property, string $code): void {
    $level = $errorCodes->getErrorLevel($code);
    $item = $entity->getPropertyItem($property);

    $this->setItemError($item, $level);
  }

  protected function setItemError(ItemInterface $item, string $level): void {
    if (in_array($level, LogItemInterface::RED_LOG_LEVELS)) {
      $item->setRedError(TRUE);
    } elseif (in_array($level, LogItemInterface::YELLOW_LOG_LEVELS)) {
      $item->setYellowError(TRUE);
    }
  }

}