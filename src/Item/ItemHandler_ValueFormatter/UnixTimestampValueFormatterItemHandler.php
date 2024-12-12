<?php

namespace App\Item\ItemHandler_ValueFormatter;

use DateTime;
use Exception;

class UnixTimestampValueFormatterItemHandler extends DateTimeValueFormatterItemHandler {

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
