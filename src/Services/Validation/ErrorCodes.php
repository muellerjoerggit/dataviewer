<?php

namespace App\Services\Validation;

use App\DaViEntity\EntityInterface;
use App\Logger\LogItems\LogItemInterface;
use App\Logger\LogItems\ValidationLogItem;
use App\Logger\Logger;

class ErrorCodes {

	public const string ERROR_CODE_MISSING = 'INT-2000';
	public const string ERROR_MISSING_PROPERTY = 'INT-1000';

	public function __construct(
		private readonly ErrorCodesRegister $errorCodesRegister,
		private readonly Logger $logger
	) {}

	public function buildError(EntityInterface $entity, string $code, array $additionalPlaceholders = []): array {
		$errorDefinition = $this->getErrorDefinition($code);

		$placeholders = $errorDefinition['placeholders'] ?? [];
		$message = $errorDefinition['message'] ?? '';

		$message = $this->buildMessage($entity, $message, $placeholders, $additionalPlaceholders);
		return [
			'message' => $message,
			'description' => $errorDefinition['description'] ?? '',
			'level' => $this->getLevel($errorDefinition)
		];
	}

	public function getErrorLevel(string $code): string {
		return $this->getLevel($this->getErrorDefinition($code));
	}

	private function buildMessage(EntityInterface $entity, string $message, array $placeholders, array $additionalPlaceholders = []): string {
		if(empty($message)) {
			$message = 'Text der Fehlermeldung fehlt';
		}

		if(empty($placeholders) && empty($additionalPlaceholders)) {
			return $message;
		}

		$finalPlaceholders = [];
		foreach ($placeholders as $placeholderKey => $property) {
			$finalPlaceholders['{' . $placeholderKey . '}'] = $entity->getPropertyValueAsString($property);
		}

		foreach ($additionalPlaceholders  as $placeholderKey => $value) {
			$finalPlaceholders['{' . $placeholderKey . '}'] = $value;
		}

		return strtr($message, $finalPlaceholders);
	}

	private function getLevel(array $errorDefinition): string {
		$level = $errorDefinition['level'] ?? '';

		if(empty($level) || !in_array($level, LogItemInterface::LOG_LEVELS)) {
			return LogItemInterface::LOG_LEVEL_WARNING;
		} else {
			return $level;
		}
	}

	private function getErrorDefinition(string $code): array {
		return $this->errorCodesRegister->getErrorDefinitionByCode($code);
	}

	public function logByCode(EntityInterface $entity, string $code, array $additionalPlaceholders = []): void {
		$error = $this->buildError($entity, $code, $additionalPlaceholders);
		$logItem = ValidationLogItem::createValidationLogItem($error['message'], $error['level'], $code);
		$this->logger->addLog($logItem);
		$entity->addLogs($logItem);
	}

	public function setItemError(EntityInterface $entity, string $property, string $code): void {
		$level = $this->getErrorLevel($code);
		$item = $entity->getPropertyItem($property);

		if(in_array($level, LogItemInterface::RED_LOG_LEVELS)) {
			$item->setCriticalError(true);
		} elseif(in_array($level, LogItemInterface::YELLOW_LOG_LEVELS)) {
			$item->setWarningError(true);
		}
	}

}
