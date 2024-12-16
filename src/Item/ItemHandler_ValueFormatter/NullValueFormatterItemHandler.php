<?php

namespace App\Item\ItemHandler_ValueFormatter;

use App\Item\ItemConfigurationInterface;
use App\Item\ItemInterface;

class NullValueFormatterItemHandler extends AbstractValueFormatterItemHandler {

  public function getStringRawFormatted(ItemInterface $item): string {
    return 'unknown ValueFormatterItemHandler';
  }

  public function getCutOffStringRawFormatted(ItemInterface $item, int $length = 50): string {
    return 'unknown ValueFormatterItemHandler';
  }

  public function getArrayRawFormatted(ItemInterface $item): array {
    return ['unknown ValueFormatterItemHandler'];
  }

  public function getArrayFormatted(ItemInterface $item): array {
    return ['unknown ValueFormatterItemHandler'];
  }

  public function getValueRawFormatted(ItemConfigurationInterface|ItemInterface $itemConfiguration, $value): string {
    return 'unknown ValueFormatterItemHandler';
  }

  public function getValueFormatted(ItemConfigurationInterface|ItemInterface $itemConfiguration, $value): string {
    return 'unknown ValueFormatterItemHandler';
  }

}
