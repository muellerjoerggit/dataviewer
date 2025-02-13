<?php

namespace App\Item\ItemHandler_Formatter;

use DateTime;
use Exception;

class UnixTimestampFormatterItemHandler extends DateTimeFormatterItemHandler {

  protected function getDateTime(mixed $dateTimeRaw): string|DateTime {
    if ($dateTimeRaw === NULL) {
      return 'NULL';
    }

    try {
      $dateTime = DateTime::createFromFormat('U', $dateTimeRaw);
    } catch (Exception $exception) {
      $dateTime = 'unknown';
    }

    return $dateTime;
  }

}
