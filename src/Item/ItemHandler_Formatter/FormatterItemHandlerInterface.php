<?php

namespace App\Item\ItemHandler_Formatter;

use App\Item\ItemConfigurationInterface;
use App\Item\ItemHandler\ItemHandlerInterface;
use App\Item\ItemInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('formatter_item_handler')]
interface FormatterItemHandlerInterface extends ItemHandlerInterface {

  public const string OUTPUT_RAW_FORMATTED = 'rawFormatted';

  public const string OUTPUT_FORMATTED = 'formatted';

  public const string OUTPUT_RAW = 'raw';

  /**
   * Return all values as string with raw values and formatted values
   */
  public function getStringRawFormatted(ItemInterface $item): string;

  /**
   * Return all values as string with raw values and formatted values -
   * truncated to the specified length
   */
  public function getCutOffStringRawFormatted(ItemInterface $item, int $length = 50): string;

  /**
   * Return all values as an array; each element is composed of the raw and
   * formatted value
   */
  public function getArrayRawFormatted(ItemInterface $item): array;

  /**
   * Return a value raw and formatted
   */
  public function getValueRawFormatted(ItemConfigurationInterface|ItemInterface $itemConfiguration, $value): string;

  /**
   * Return value as formatted string
   */
  public function getStringFormatted(ItemInterface $item): string;

  /**
   * Return value as raw and formatted string - truncated to the specified
   * length
   */
  public function getCutOffStringFormatted(ItemInterface $item, int $length = 50): string;

  /**
   * Return all values as an array; an array item consists of formatted value
   */
  public function getArrayFormatted(ItemInterface $item): array;

  /**
   * Return value formatted
   */
  public function getValueFormatted(ItemConfigurationInterface|ItemInterface $itemConfiguration, $value): string;

  public function getPossibleFormats(): array;

}
