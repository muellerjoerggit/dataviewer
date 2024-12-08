<?php

namespace App\Item\ItemHandler_ValueFormatter;

use App\Item\ItemConfigurationInterface;
use App\Item\ItemInterface;

abstract class AbstractValueFormatterItemHandler implements ValueFormatterItemHandlerInterface {

	abstract public function getArrayFormatted(ItemInterface $item): array;

	public function getStringFormatted(ItemInterface $item): string {
		return implode(', ', $this->getArrayFormatted($item));
	}

	public function getCutOffStringFormatted(ItemInterface $item, int $length = 50): string {
		return mb_substr($this->getStringFormatted($item), 0, $length);
	}

	abstract public function getArrayRawFormatted(ItemInterface $item): array;

	public function getStringRawFormatted(ItemInterface $item): string {
		return implode(', ', $this->getArrayRawFormatted($item));
	}

	public function getCutOffStringRawFormatted(ItemInterface $item, int $length = 50): string {
		return mb_substr($this->getStringRawFormatted($item), 0, $length);
	}

	abstract public function getValueRawFormatted(ItemConfigurationInterface | ItemInterface $itemConfiguration, $value): string ;

	abstract public function getValueFormatted(ItemConfigurationInterface | ItemInterface $itemConfiguration, $value): string;

	public function getPossibleFormats(): array {
		return [];
	}

	protected function getDefaultFormat(): mixed {
		$formats = $this->getPossibleFormats();
		return reset($formats);
	}

	protected function getFormat(string $type): mixed {
		$formats = $this->getPossibleFormats();
		if(!isset($formats[$type])) {
			return $this->getDefaultFormat();
		}

		return $formats[$type];
	}

	protected function convertToFormat($value, $format = ''): mixed {
		return $value;
	}

}
