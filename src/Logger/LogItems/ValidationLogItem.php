<?php

namespace App\Logger\LogItems;

use DateTime;

class ValidationLogItem extends LogItem {

  public static function createValidationLogItem(string $message = '', string $level = LogItemInterface::LOG_LEVEL_INFO, string $code = '', string $title = 'Validierung', ?DateTime $dateTime = NULL): ValidationLogItem {
    if ($dateTime === NULL) {
      $dateTime = new DateTime();
    }

    $logItem = new static($message, $title, $level, $dateTime);
    return $logItem;
  }

}
