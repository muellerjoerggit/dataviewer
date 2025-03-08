<?php

namespace App\Services\Validation;

use App\DaViEntity\EntityInterface;
use App\Logger\Logger;
use App\Logger\LogItems\LogItemInterface;
use App\Logger\LogItems\ValidationLogItem;
use App\Logger\LogLevels;

class ErrorCodes {

  public const string ERROR_CODE_MISSING = 'INT-2000';

  public const string ERROR_MISSING_PROPERTY = 'INT-1000';

  public function __construct(
    private readonly ErrorCodesRegister $errorCodesRegister,
    private readonly Logger $logger
  ) {}

  public function logByCode(EntityInterface $entity, string $code, array $additionalPlaceholders = []): void {
    $error = $this->buildError($entity, $code, $additionalPlaceholders);
    $logItem = ValidationLogItem::createValidationLogItem($error['message'], $error['level'], $code);
    $this->logger->addLog($logItem);
    $entity->addLogs($logItem);
  }

  public function buildError(EntityInterface $entity, string $code, array $additionalPlaceholders = []): array {
    $errorDefinition = $this->getErrorDefinition($code);

    $placeholders = $errorDefinition['placeholders'] ?? [];
    $message = $errorDefinition['message'] ?? '';

    $message = $this->buildMessage($entity, $message, $placeholders, $additionalPlaceholders);
    return [
      'message' => $message,
      'description' => $errorDefinition['description'] ?? '',
      'level' => $this->getLevel($errorDefinition),
    ];
  }

  private function getErrorDefinition(string $code): array {
    return $this->errorCodesRegister->getErrorDefinitionByCode($code);
  }

  private function buildMessage(EntityInterface $entity, string $message, array $placeholders, array $additionalPlaceholders = []): string {
    if (empty($message)) {
      $message = 'Text der Fehlermeldung fehlt';
    }

    if (empty($placeholders) && empty($additionalPlaceholders)) {
      return $message;
    }

    $finalPlaceholders = [];
    foreach ($placeholders as $placeholderKey => $property) {
      $finalPlaceholders['{' . $placeholderKey . '}'] = $entity->getPropertyValueAsString($property);
    }

    foreach ($additionalPlaceholders as $placeholderKey => $value) {
      $finalPlaceholders['{' . $placeholderKey . '}'] = $value;
    }

    return strtr($message, $finalPlaceholders);
  }

  private function getLevel(array $errorDefinition): string {
    $level = $errorDefinition['level'] ?? '';

    if (empty($level) || !in_array($level, LogLevels::LOG_LEVELS)) {
      return LogLevels::WARNING;
    } else {
      return $level;
    }
  }

  public function setItemError(EntityInterface $entity, string $property, string $code): void {
    $level = $this->getErrorLevel($code);
    $item = $entity->getPropertyItem($property);

    if (in_array($level, LogLevels::RED_LOG_LEVELS)) {
      $item->setCriticalError(TRUE);
    } elseif (in_array($level, LogLevels::YELLOW_LOG_LEVELS)) {
      $item->setWarningError(TRUE);
    }
  }

  public function getErrorLevel(string $code): string {
    return $this->getLevel($this->getErrorDefinition($code));
  }

}
