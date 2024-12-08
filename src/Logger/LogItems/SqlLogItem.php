<?php

namespace App\Logger\LogItems;

class SqlLogItem extends LogItem {

	public static function createSqlLogItem(string $sql, string $message = '', $title = '', string $level = LogItemInterface::LOG_LEVEL_INFO, $dateTime = null, $rawLogs = []): LogItemInterface {
		if($dateTime === null) {
			$dateTime = new \DateTime();
		}

		$logItem = new static($message, $title, $level, $dateTime, $rawLogs);
		$logItem->addRawLogs($sql);
		return $logItem;
	}

	public function getTitle(): string {
		if(!empty($this->title)) {
			return $this->title;
		}

		return 'SQL-Abfrage';
	}

}
