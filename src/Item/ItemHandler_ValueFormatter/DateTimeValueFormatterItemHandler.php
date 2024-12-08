<?php

namespace App\Item\ItemHandler_ValueFormatter;

use App\Item\ItemConfigurationInterface;
use App\Item\ItemInterface;
use DateTime;

class DateTimeValueFormatterItemHandler extends AbstractValueFormatterItemHandler {

	protected function formatValue(DateTime $dateTime): string {
		return $dateTime->format('d.m.Y H:i');
	}

	protected function getDateTime(mixed $dateTimeRaw): string | DateTime {
		if($dateTimeRaw === null) {
			return 'NULL';
		}

		if($dateTimeRaw === '0000-00-00 00:00:00') {
			return $dateTimeRaw;
		}

		try {
			$dateTime = new DateTime($dateTimeRaw);
		} catch (\Exception $exception) {
			$dateTime = 'unknown';
		}

		return $dateTime;
	}

	public function getArrayFormatted(ItemInterface $item): array {
		$ret = [];

		foreach ($item->getValuesAsOneDimensionalArray() as $key => $dateTimeRaw) {
			$ret[$key] =  $this->getValueFormatted($item, $dateTimeRaw);
		}

		return $ret;
	}

	public function getArrayRawFormatted(ItemInterface $item): array {
		$ret = [];

		foreach ($item->getValuesAsOneDimensionalArray() as $key => $dateTimeRaw) {
			$ret[$key] = $this->getValueRawFormatted($item, $dateTimeRaw);
		}

		return $ret;
	}

	public function getValueRawFormatted(ItemConfigurationInterface|ItemInterface $itemConfiguration, $value): string {
		$dateTime = $this->getDateTime($value);

		if(!($dateTime instanceof DateTime)) {
			return $dateTime;
		}

		return $value . ' (' . $this->formatValue($dateTime) . ')';
	}


	public function getValueFormatted(ItemConfigurationInterface | ItemInterface $itemConfiguration, $value): string {
		$dateTime = $this->getDateTime($value);

		if(!($dateTime instanceof DateTime)) {
			return $dateTime;
		}

		return $this->formatValue($dateTime);
	}

	public function getPossibleFormats(): array {
		return [
			'dmYHi' => [
				'label' => 'deutsches Datum + Uhrzeit',
				'description' => 'Tag.Monat.Jahr und Uhrzeit Stunden:Minuten',
				'format' => 'd.m.Y H:i'
			],
			'dmY' => [
				'label' => 'deutsches Datum',
				'description' => 'Tag.Monat.Jahr',
				'format' => 'd.m.Y'
			],
			'Hi' => [
				'label' => 'Uhrzeit',
				'description' => 'Uhrzeit Stunden:Minuten',
				'format' => 'H:i'
			],
			'database' => [
				'label' => 'Datenbankformat',
				'description' => 'Jahr-Monat-Tag und Uhrzeit Stunden:Minuten:Sekunden',
				'format' => 'Y-m-d H:i:s'
			]
		];
	}
}
