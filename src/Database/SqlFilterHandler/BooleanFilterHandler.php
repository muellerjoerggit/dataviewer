<?php

namespace App\Database\SqlFilterHandler;

use App\Database\DaViQueryBuilder;
use App\Database\SqlFilterHandler\Attribute\SqlFilterDefinitionInterface;
use App\Database\SqlFilter\SqlFilterInterface;
use App\DaViEntity\Schema\EntitySchema;

class BooleanFilterHandler extends AbstractFilterHandler{

  protected const string COMPONENT_NAME = 'SelectFilter';

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

  public function getFilterComponent(SqlFilterDefinitionInterface $filterDefinition, EntitySchema $schema): array {
    $component = $this->getFilterComponentInternal($filterDefinition);

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

    $component['additional']['possibleValues'] = $possibleValues;

		return $component;
	}

}
