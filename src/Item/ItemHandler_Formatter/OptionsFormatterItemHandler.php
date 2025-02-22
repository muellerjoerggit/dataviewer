<?php

namespace App\Item\ItemHandler_Formatter;

use App\Item\ItemConfigurationInterface;
use App\Item\ItemInterface;
use App\Item\Property\Attribute\OptionItemSettingDefinition;

class OptionsFormatterItemHandler extends AbstractFormatterItemHandler {

  public function getArrayRawFormatted(ItemInterface $item): array {
    return array_map(function($value) use ($item) {
      return $this->getValueRawFormatted($item, $value);
    }, $item->getValuesAsOneDimensionalArray());
  }

  public function getValueRawFormatted(ItemConfigurationInterface|ItemInterface $itemConfiguration, $value): string {
    $options = $this->getOptions($itemConfiguration);
    return $value . ' (' . $this->formatValueByOptions($options, $value) . ')';
  }

  protected function getOptions(ItemConfigurationInterface | ItemInterface $itemConfiguration): OptionItemSettingDefinition | null {
    if ($itemConfiguration instanceof ItemInterface) {
      $itemConfiguration = $itemConfiguration->getConfiguration();
    }

    if($itemConfiguration->hasSetting(OptionItemSettingDefinition::class)) {
      return $itemConfiguration->getSetting(OptionItemSettingDefinition::class);
    }

    return null;
  }

  protected function formatValueByOptions(OptionItemSettingDefinition | null $options, mixed $value): string {
    if (is_scalar($value) && $options instanceof OptionItemSettingDefinition && $options->hasOption($value)) {
      $ret = $options->getLabel($value);
    } elseif (is_scalar($value)) {
      $ret = '(unbekannte Option)';
    } else {
      $ret = 'Fehler';
    }

    return $ret;
  }

  public function getArrayFormatted(ItemInterface $item): array {
    $ret = [];
    foreach ($item->getValuesAsOneDimensionalArray() as $key => $value) {
      $ret[$key] = $this->getValueFormatted($item, $value);
    }
    return $ret;
  }

  public function getValueFormatted(ItemConfigurationInterface|ItemInterface $itemConfiguration, $value): string {
    $options = $this->getOptions($itemConfiguration);
    return $this->formatValueByOptions($options, $value);
  }

}
