<?php

namespace App\Database\SqlFilter;

use App\DaViEntity\Schema\EntitySchema;
use App\Item\ItemConfigurationInterface;

class SqlFilterDefinitionBuilder {

	public function __construct(
		private readonly SqlFilterHandlerLocator $filterHandlerLocator
	) {}

	public function buildFilterDefinition(EntitySchema $schema, array $filterArray): void {
    $filterDefinition = SqlFilterDefinition::createFromArray($filterArray);

    $this->fillEntityFilterDefinition($filterDefinition, $filterArray);
    $schema->addFilter($filterDefinition);
	}

	public function fillEntityFilterDefinition(SqlFilterDefinitionInterface $entityFilterDefinition, $filterArray): void {

		if(isset($filterArray['settings'])) {
			$entityFilterDefinition->setSettings($filterArray['settings']);
		}

		$writableKeys = ['title', 'description', 'default_value', 'property'];
		foreach ($writableKeys as $key) {
			if(!isset($filterArray[$key])) {
				continue;
			}

			switch ($key) {
				case 'title':
					$entityFilterDefinition->setTitle($filterArray[$key]);
					break;
				case 'description':
					$entityFilterDefinition->setDescription($filterArray[$key]);
					break;
				case 'property':
					$entityFilterDefinition->setProperty($filterArray[$key]);
					break;
				case 'default_value':
					$entityFilterDefinition->setDefaultValue($filterArray[$key]);
					break;
			}
		}
	}

	public function buildComponentFromFilterDefinition(SqlFilterDefinitionInterface $filterDefinition, EntitySchema $schema): array {
		$handler = $this->filterHandlerLocator->getFilterHandlerFromFilterDefinition($filterDefinition);
		$ret = [];

		$defaultComponent = [
			'component' => '',
			'name' => '',
			'title' => '',
			'mandatory' => false,
			'description' => '',
			'default_value' => null,
			'possible_values' => [],
			'additional' => []
		];

		$component = $handler->getFilterComponent($filterDefinition, $schema);
		if(!empty($component)) {
			$component = array_merge($defaultComponent, $component);
			$ret = $component;
		}

		return $ret;
	}

  public function calculateFilterHash(array $filterConfig, array $additional = []): string {
    $string = $this->buildHashString('', $filterConfig);
    if(!empty($additional)) {
      $string = $this->buildHashString($string, $additional);
    }

    return sha1($string);
  }

  protected function buildHashString(string $string, array $input): string {
    array_walk_recursive(
      $input,
      function ($value, $key) use (&$string) {
        $value = is_array($value) ? implode(',', $value) : $value;
        $string = empty($string) ? $key . '=>' . $value : $string . '::' . $key . '=>' . $value;
      }
    );
    return $string;
  }

}
