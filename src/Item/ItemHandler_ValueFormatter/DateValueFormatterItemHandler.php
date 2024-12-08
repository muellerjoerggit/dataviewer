<?php

namespace App\Item\ItemHandler_ValueFormatter;

use DateTime;

class DateValueFormatterItemHandler extends DateTimeValueFormatterItemHandler {

	protected function formatValue(DateTime $dateTime): string {
		return $dateTime->format('d.m.Y');
	}

	protected function getDateTime(mixed $dateTimeRaw): string | DateTime {
		if($dateTimeRaw === null) {
			return 'NULL';
		}

		if($dateTimeRaw === '0000-00-00 00:00:00' || $dateTimeRaw === '0000-00-00') {
			return $dateTimeRaw;
		}

		try {
			$dateTime = new DateTime($dateTimeRaw);
		} catch (\Exception $exception) {
			$dateTime = 'unknown';
		}

		return $dateTime;
	}

	public function getPossibleFormats(): array {
		return [
			'dmY' => [
				'label' => 'deutsches Datum',
				'description' => 'Tag.Monat.Jahr',
				'format' => 'd.m.Y'
			],
			'database' => [
				'label' => 'Datenbankformat',
				'description' => 'Jahr-Monat-Tag',
				'format' => 'Y-m-d'
			]
		];
	}

}
