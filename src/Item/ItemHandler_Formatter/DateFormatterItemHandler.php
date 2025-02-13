<?php

namespace App\Item\ItemHandler_Formatter;

use DateTime;
use Exception;

class DateFormatterItemHandler extends DateTimeFormatterItemHandler {

  public function getPossibleFormats(): array {
    return [
      'dmY' => [
        'label' => 'deutsches Datum',
        'description' => 'Tag.Monat.Jahr',
        'format' => 'd.m.Y',
      ],
      'database' => [
        'label' => 'Datenbankformat',
        'description' => 'Jahr-Monat-Tag',
        'format' => 'Y-m-d',
      ],
    ];
  }

  protected function formatValue(DateTime $dateTime): string {
    return $dateTime->format('d.m.Y');
  }

  protected function getDateTime(mixed $dateTimeRaw): string|DateTime {
    if ($dateTimeRaw === NULL) {
      return 'NULL';
    }

    if ($dateTimeRaw === '0000-00-00 00:00:00' || $dateTimeRaw === '0000-00-00') {
      return $dateTimeRaw;
    }

    try {
      $dateTime = new DateTime($dateTimeRaw);
    } catch (Exception $exception) {
      $dateTime = 'unknown';
    }

    return $dateTime;
  }

}
