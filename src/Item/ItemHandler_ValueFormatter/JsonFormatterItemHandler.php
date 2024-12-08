<?php

namespace App\Item\ItemHandler_ValueFormatter;

use App\Item\ItemConfigurationInterface;
use App\Item\ItemInterface;

class JsonFormatterItemHandler extends AbstractValueFormatterItemHandler {

	public function getArrayRawFormatted(ItemInterface $item): array {
		return $this->getArrayFormatted($item);
	}

	public function getArrayFormatted(ItemInterface $item): array {
		$ret = [];
		foreach ($item->getValuesAsOneDimensionalArray() as $key => $value) {
			$ret[$key] = $this->getValueFormatted($item, $value);
		}
		return $ret;
	}

	public function getValueRawFormatted(ItemConfigurationInterface | ItemInterface $itemConfiguration, $value): string {
		return $this->getValueFormatted($itemConfiguration, $value);
	}

	public function getValueFormatted(ItemConfigurationInterface | ItemInterface $itemConfiguration, $value): string {
		$json = json_decode($value, true);

		if(!$json) {
			return $value;
		}

		return json_encode($json, JSON_PRETTY_PRINT);
	}
}
