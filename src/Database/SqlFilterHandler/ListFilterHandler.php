<?php

namespace App\Database\SqlFilterHandler;

use App\Database\DaViQueryBuilder;
use App\Database\SqlFilter\SqlFilterDefinitionInterface;
use App\DaViEntity\Schema\EntitySchema;

/**
 * filter handler for a list of values separated by a character
 * possible separators: semicolon, comma, space, new line
 */
class ListFilterHandler extends InFilterHandler {

	public function setWhereIn(DaViQueryBuilder $queryBuilder, string $column, $values, int $dataType): void {
		$values = $this->processValues($values);

		if($values === false) {
			return;
		}

		parent::setWhereIn($queryBuilder, $column, $values, $dataType);
	}

	protected function processValues(mixed $values): array | bool {

		if(is_array($values)) {
			$ret = [];
			foreach ($values as $value) {
				$value = $this->processValues($value);
				if($value !== false && is_array($value)) {
					$ret = array_merge($ret, $value);
				}
			}
			return $ret;
		}

		if(!is_string($values)) {
			return false;
		}

		$separator = $this->checkSeparator($values);

		if($separator === false) {
			return false;
		}

		if(empty($separator)) {
			return [$values];
		}

		$ret = explode($separator, $values);
		return array_map('trim', $ret);
	}

	protected function checkSeparator(string $values): string | bool {
		$separators = ["\n", ';', ',', ' '];
		$separator = '';

		foreach ($separators as $char) {
			if(str_contains($values, $char) && empty($separator)) {
				$separator = $char;
			} elseif (str_contains($values, $char) && !empty($separator) && $char !== ' ') {
				return false;
			}
		}

		return $separator;
	}

  public function getFilterComponent(SqlFilterDefinitionInterface $filterDefinition, EntitySchema $schema): array {
		return [
			'component' => 'ListFilter',
			'name' => $filterDefinition->getKey(),
			'title' => $filterDefinition->getTitle(),
			'description' => $filterDefinition->getDescription(),
			'defaultValue' => $filterDefinition->getDefaultValue(),
		];
	}

}
