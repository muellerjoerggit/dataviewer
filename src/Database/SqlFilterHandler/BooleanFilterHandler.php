<?php

namespace App\Database\SqlFilterHandler;

use App\Database\DaViQueryBuilder;
use App\Database\SqlFilter\SqlFilterDefinitionInterface;
use App\Database\SqlFilter\SqlFilterHandlerInterface;
use App\Database\SqlFilter\SqlFilterInterface;
use App\DaViEntity\Schema\EntitySchema;

class BooleanFilterHandler extends AbstractFilterHandler{

  public function extendQueryWithFilter(DaViQueryBuilder $queryBuilder, SqlFilterInterface $filter, EntitySchema $schema): void {
    $value = $filter->getValue();
    $column = $this->getColumn($filter, $schema);
    $value = is_array($value) ? reset($value) : $value;
    $value = is_int($value) ? (bool)$value : $value;

    if(!is_bool($value) || empty($column)) {
      return;
    }

    $queryBuilder->andWhere(
      $queryBuilder->expr()->eq($column, $value ? '1' : '0')
    );
  }

  public function getFilterComponent(SqlFilterDefinitionInterface $filterDefinition, EntitySchema $schema, string $filterKey = ''): array {
		$property = $filterDefinition->getProperty();
		$config = $schema->getProperty($property);
		$options = $config->getSetting('options', []);

		$possibleValues = [];

		foreach($options as $options_key => $option) {
			$possibleValues[] = [
				'optionId' => $options_key,
				'label' => $option['label'] ?? '',
				'description' => $option['description'] ?? ''
			];
		}

		return [
			'component' => 'SelectFilter',
      'type' => $filterDefinition->getType(),
			'name' => $filterDefinition->getKey(),
			'title' => $filterDefinition->getTitle(),
			'description' => $filterDefinition->getDescription(),
			'defaultValue' => $filterDefinition->getDefaultValue(),
			'possibleValues' => $possibleValues
		];
	}

}
