<?php

namespace App\Item\ItemHandler_Formatter;

use App\Item\ItemConfigurationInterface;
use App\Item\ItemInterface;

abstract class AbstractFormatterItemHandler implements FormatterItemHandlerInterface {

  public function getCutOffStringFormatted(ItemInterface $item, int $length = 50): string {
    return mb_substr($this->getStringFormatted($item), 0, $length);
  }

  public function getStringFormatted(ItemInterface $item): string {
    return implode(', ', $this->getArrayFormatted($item));
  }

  abstract public function getArrayFormatted(ItemInterface $item): array;

  public function getCutOffStringRawFormatted(ItemInterface $item, int $length = 50): string {
    return mb_substr($this->getStringRawFormatted($item), 0, $length);
  }

  public function getStringRawFormatted(ItemInterface $item): string {
    return implode(', ', $this->getArrayRawFormatted($item));
  }

  abstract public function getArrayRawFormatted(ItemInterface $item): array;

  abstract public function getValueRawFormatted(ItemConfigurationInterface|ItemInterface $itemConfiguration, $value): string;

  abstract public function getValueFormatted(ItemConfigurationInterface|ItemInterface $itemConfiguration, $value): string;

  protected function getFormat(string $type): mixed {
    $formats = $this->getPossibleFormats();
    if (!isset($formats[$type])) {
      return $this->getDefaultFormat();
    }

    return $formats[$type];
  }

  public function getPossibleFormats(): array {
    return [];
  }

  protected function getDefaultFormat(): mixed {
    $formats = $this->getPossibleFormats();
    return reset($formats);
  }

  protected function convertToFormat($value, $format = ''): mixed {
    return $value;
  }

}
