<?php

namespace App\Item\ItemHandler_Formatter;

use App\Item\ItemConfigurationInterface;
use App\Item\ItemInterface;

class JsonFormatterItemHandler extends AbstractFormatterItemHandler {

  public function getArrayRawFormatted(ItemInterface $item): array {
    return $this->getArrayFormatted($item);
  }

  public function getArrayFormatted(ItemInterface $item): array {
    $ret = array_map(function($value) use ($item) {
      return $this->getValueFormatted($item, $value);
    }, $item->getValuesAsOneDimensionalArray());
    return $ret;
  }

  public function getValueFormatted(ItemConfigurationInterface|ItemInterface $itemConfiguration, $value): string {
    $json = json_decode($value, TRUE);

    if (!$json) {
      return $value;
    }

    return json_encode($json, JSON_PRETTY_PRINT);
  }

  public function getValueRawFormatted(ItemConfigurationInterface|ItemInterface $itemConfiguration, $value): string {
    return $this->getValueFormatted($itemConfiguration, $value);
  }

}
