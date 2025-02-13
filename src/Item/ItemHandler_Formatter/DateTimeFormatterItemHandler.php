<?php

namespace App\Item\ItemHandler_Formatter;

use App\Item\ItemConfigurationInterface;
use App\Item\ItemInterface;
use DateTime;
use Exception;

class DateTimeFormatterItemHandler extends AbstractFormatterItemHandler {

  public function getArrayFormatted(ItemInterface $item): array {
    $ret = array_map(function($dateTimeRaw) use ($item) {
      return $this->getValueFormatted($item, $dateTimeRaw);
    }, $item->getValuesAsOneDimensionalArray());

    return $ret;
  }

  public function getValueFormatted(ItemConfigurationInterface|ItemInterface $itemConfiguration, $value): string {
    $dateTime = $this->getDateTime($value);

    if (!($dateTime instanceof DateTime)) {
      return $dateTime;
    }

    return $this->formatValue($dateTime);
  }

  protected function getDateTime(mixed $dateTimeRaw): string|DateTime {
    if ($dateTimeRaw === NULL) {
      return 'NULL';
    }

    if ($dateTimeRaw === '0000-00-00 00:00:00') {
      return $dateTimeRaw;
    }

    try {
      $dateTime = new DateTime($dateTimeRaw);
    } catch (Exception $exception) {
      $dateTime = 'unknown';
    }

    return $dateTime;
  }

  protected function formatValue(DateTime $dateTime): string {
    return $dateTime->format('d.m.Y H:i');
  }

  public function getArrayRawFormatted(ItemInterface $item): array {
    $ret = array_map(function($dateTimeRaw) use ($item) {
      return $this->getValueRawFormatted($item, $dateTimeRaw);
    }, $item->getValuesAsOneDimensionalArray());

    return $ret;
  }

  public function getValueRawFormatted(ItemConfigurationInterface|ItemInterface $itemConfiguration, $value): string {
    $dateTime = $this->getDateTime($value);

    if (!($dateTime instanceof DateTime)) {
      return $dateTime;
    }

    return $value . ' (' . $this->formatValue($dateTime) . ')';
  }

  public function getPossibleFormats(): array {
    return [
      'dmYHi' => [
        'label' => 'deutsches Datum + Uhrzeit',
        'description' => 'Tag.Monat.Jahr und Uhrzeit Stunden:Minuten',
        'format' => 'd.m.Y H:i',
      ],
      'dmY' => [
        'label' => 'deutsches Datum',
        'description' => 'Tag.Monat.Jahr',
        'format' => 'd.m.Y',
      ],
      'Hi' => [
        'label' => 'Uhrzeit',
        'description' => 'Uhrzeit Stunden:Minuten',
        'format' => 'H:i',
      ],
      'database' => [
        'label' => 'Datenbankformat',
        'description' => 'Jahr-Monat-Tag und Uhrzeit Stunden:Minuten:Sekunden',
        'format' => 'Y-m-d H:i:s',
      ],
    ];
  }

}
