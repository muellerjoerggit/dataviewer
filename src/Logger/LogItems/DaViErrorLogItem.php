<?php

namespace App\Logger\LogItems;

use App\Logger\LogItems\LogItem;

class DaViErrorLogItem extends LogItem {

	public static function createDaViErrorLogItem(string $message = '', string $title = '', string $level = LogItemInterface::LOG_LEVEL_DAVI_NOTICE, ?\DateTime $dateTime = null): DaViErrorLogItem {
		if($dateTime === null) {
			$dateTime = new \DateTime();
		}

		$logItem = new static($message, $title, $level, $dateTime);
		$backtrace = json_encode(debug_backtrace());
		if(is_string($backtrace)) {
			$logItem->addRawLogs(json_encode(debug_backtrace()));
		}
		return $logItem;
	}

}
