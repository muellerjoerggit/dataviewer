<?php

namespace App\Logger\LogItems;

use App\Logger\LogLevels;
use DateTime;

class ValidationLogItem extends LogItem {

  public static function createValidationLogItem(string $message = '', string $level = LogLevels::INFO, string $code = '', string $title = 'Validierung', ?DateTime $dateTime = NULL): ValidationLogItem {
    if ($dateTime === NULL) {
      $dateTime = new DateTime();
    }

    $logItem = new static($message, $title, $level, $dateTime);
    return $logItem;
  }

}
