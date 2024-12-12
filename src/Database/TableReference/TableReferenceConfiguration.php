<?php

namespace App\Database\TableReference;

use App\Item\ItemConfigurationInterface;
use App\Services\AppNamespaces;

class TableReferenceConfiguration implements TableReferenceConfigurationInterface {

	private string $name;
	private string $handler;
	private array $settings = [];
	private array $additionalProperties = [];
	private array $columns = [];

	public function __construct(string $handler) {
		$this->handler = $handler;
	}

	public static function createFromSchemaSetting(array | string $setting, string $additionalTableKey): TableReferenceConfigurationInterface | bool {
		$handler = '';

		if(is_string($setting)) {
			$handler = $setting;
		} elseif (is_array($setting)) {
			$handler = key($setting) ?? '';
		}

		if(empty($handler)) {
			return false;
		}

		$additionalTableConfiguration = new static($handler);
		$additionalTableConfiguration->setSettings($setting[$handler] ?? []);
		$additionalTableConfiguration->setName($additionalTableKey);

		return $additionalTableConfiguration;
	}

	public function getName(): string {
		return $this->name;
	}

	public function setName(string $name): TableReferenceConfigurationInterface {
		$this->name = $name;
		return $this;
	}

	public function setMandatory(bool $mandatory): TableReferenceConfigurationInterface {
		$this->settings['mandatory'] = $mandatory;
		return $this;
	}

	public function isMandatory(): bool {
		return $this->settings['mandatory'] ?? false;
	}

	public function getHandler(): string {
    return AppNamespaces::buildNamespace(AppNamespaces::TABLE_REFERENCE_HANDLER, $this->handler);
	}

	public function setSettings(array $settings): TableReferenceConfigurationInterface {
		$this->settings = $settings;
		return $this;
	}

	public function getSetting(string $setting, $default = null): mixed {
		return $this->settings[$setting] ?? $default;
	}

	public function setSetting(string $setting, mixed $value): TableReferenceConfiguration {
		$this->settings[$setting] = $value;
		return $this;
	}

	public function addAdditionalProperty(ItemConfigurationInterface $itemConfiguration): TableReferenceConfiguration {
		$name = $itemConfiguration->getItemName();
		$this->additionalProperties[$name] = $itemConfiguration;
		return $this;
	}

	public function iterateAdditionalProperties(): \Generator {
		foreach($this->additionalProperties as $name => $additionalProperty) {
			yield $name => $additionalProperty;
		}
	}

	private function buildColumns(): void {
		foreach ($this->iterateAdditionalProperties() as $name => $additionalProperty) {
			if(!($additionalProperty instanceof ItemConfigurationInterface)) {
				continue;
			}

			$column = $additionalProperty->getColumn();

			if(empty($column)) {
				continue;
			}

			$this->columns[$name] = $column;
		}
	}

	public function iterateColumns(): \Generator {
		if(empty($this->columns)) {
			$this->buildColumns();
		}
		foreach($this->columns as $name => $column) {
			yield $name => $column;
		}
	}

	public function getColumns(): array {
		if(empty($this->columns)) {
			$this->buildColumns();
		}
		return $this->columns ?? [];
	}

}
