<?php

namespace App\Logger\LogItems;

use DateTime;

class DaViErrorLogItem extends LogItem {

  public static function createDaViErrorLogItem(string $message = '', string $title = '', string $level = LogItemInterface::LOG_LEVEL_INTERNAL_NOTICE, ?DateTime $dateTime = NULL): DaViErrorLogItem {
    if ($dateTime === NULL) {
      $dateTime = new DateTime();
    }

    $logItem = new static($message, $title, $level, $dateTime);
    $backtrace = json_encode(debug_backtrace());
    if (is_string($backtrace)) {
      $logItem->addRawLogs(json_encode(debug_backtrace()));
    }
    return $logItem;
  }

}
