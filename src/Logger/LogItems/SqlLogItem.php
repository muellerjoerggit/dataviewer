<?php

namespace App\Logger\LogItems;

use App\Logger\LogLevels;
use DateTime;

class SqlLogItem extends LogItem {

  public static function createSqlLogItem(string $sql, string $message = '', $title = '', string $level = LogLevels::INFO, $dateTime = NULL, $rawLogs = []): LogItemInterface {
    if ($dateTime === NULL) {
      $dateTime = new DateTime();
    }

    $logItem = new static($message, $title, $level, $dateTime, $rawLogs);
    $logItem->addRawLogs($sql);
    return $logItem;
  }

  public function getTitle(): string {
    if (!empty($this->title)) {
      return $this->title;
    }

    return 'SQL-Abfrage';
  }

}
