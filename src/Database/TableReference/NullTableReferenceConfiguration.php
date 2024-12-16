<?php

namespace App\Database\TableReference;

use App\Database\TableReferenceHandler\NullTableReferenceHandler;
use App\Item\ItemConfigurationInterface;

class NullTableReferenceConfiguration implements TableReferenceConfigurationInterface {

	public function __construct(string $handler) {
	}

	public static function createNullAdditionalConfiguration(): TableReferenceConfigurationInterface {
		return new static('NullTableReferenceHandler');
	}

	public function getHandler(): string {
		return NullTableReferenceHandler::class;
	}

	public function setSettings(array $settings): TableReferenceConfigurationInterface {
		return $this;
	}

	public function getSetting(string $setting, $default = null): mixed {
		return $default;
	}

	public function setSetting(string $setting, mixed $value): TableReferenceConfigurationInterface {
		return $this;
	}

	public function addAdditionalProperty(ItemConfigurationInterface $itemConfiguration): TableReferenceConfigurationInterface {
		return $this;
	}

	public function iterateAdditionalProperties(): \Generator {
		yield from [];
	}


	public function iterateColumns(): \Generator {
		yield from [];
	}

	public function getColumns(): array {
		return [];
	}

	public function setMandatory(bool $mandatory): TableReferenceConfigurationInterface {
		return $this;
	}

	public function isMandatory(): bool {
		return false;
	}

}
