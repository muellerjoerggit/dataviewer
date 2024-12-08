<?php

namespace App\Logger\LogItems;

class ValidationLogItem extends LogItem {

	public static function createValidationLogItem(string $message = '', string $level = LogItemInterface::LOG_LEVEL_INFO, string $code = '', string $title = 'Validierung', ?\DateTime $dateTime = null): ValidationLogItem {
		if($dateTime === null) {
			$dateTime = new \DateTime();
		}

		$logItem = new static($message, $title, $level, $dateTime);
		return $logItem;
	}
}
