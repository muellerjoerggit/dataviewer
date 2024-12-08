<?php

namespace App\Item\ItemHandler_ValueFormatter;

use App\Item\ItemConfigurationInterface;
use App\Item\ItemInterface;

class OptionsValueFormatterItemHandler extends AbstractValueFormatterItemHandler {

	public function getArrayRawFormatted(ItemInterface $item): array {
		$ret = [];
		foreach ($item->getValuesAsOneDimensionalArray() as $key => $value) {
			$ret[$key] = $this->getValueRawFormatted($item, $value);
		}
		return $ret;
	}

	public function getValueRawFormatted(ItemConfigurationInterface | ItemInterface $itemConfiguration, $value): string {
		$options = $this->getOptions($itemConfiguration);
		return $value . ' (' . $this->formatValueByOptions($options, $value) . ')';
	}

	protected function getOptions(ItemConfigurationInterface | ItemInterface $itemConfiguration): array {
		if($itemConfiguration instanceof ItemInterface) {
			$itemConfiguration = $itemConfiguration->getConfiguration();
		}
		return $itemConfiguration->getSetting('options', []);
	}

	protected function formatValueByOptions(array $options, mixed $value): string {
		if(is_scalar($value) && (isset($options[$value]))) {
			$ret = $options[$value]['label'] ?? 'ohne Label';;
		} elseif (is_scalar($value)) {
			$ret = '(unbekannte Option)';
		} else {
			$ret = 'Fehler';
		}

		return $ret;
	}

	public function getValueFormatted(ItemConfigurationInterface | ItemInterface $itemConfiguration, $value): string {
		$options = $this->getOptions($itemConfiguration);
		return $this->formatValueByOptions($options, $value);
	}

	public function getArrayFormatted(ItemInterface $item): array {
		$ret = [];
		foreach ($item->getValuesAsOneDimensionalArray() as $key => $value) {
			$ret[$key] = $this->getValueFormatted($item, $value);
		}
		return $ret;
	}
}
