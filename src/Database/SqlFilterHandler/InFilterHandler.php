<?php

namespace App\Database\SqlFilterHandler;

use App\Database\DaViQueryBuilder;
use App\Database\SqlFilter\SqlFilterDefinitionInterface;
use App\Database\SqlFilter\SqlFilterHandlerInterface;
use App\Database\SqlFilter\SqlFilterInterface;
use App\DaViEntity\Schema\EntitySchema;

class InFilterHandler extends AbstractFilterHandler implements InFilterInterface, SqlFilterHandlerInterface {

  public function extendQueryWithFilter(DaViQueryBuilder $queryBuilder, SqlFilterInterface $filter, EntitySchema $schema): void {
    $value = $filter->getValue();
    $property = $this->getProperty($filter, $schema);
    $column = $schema->getColumn($property);
    $dataType = $schema->getProperty($property)->getQueryParameterType(true);


    $this->setWhereIn($queryBuilder, $column, $value, $dataType);
  }

	protected function setWhereIn(DaViQueryBuilder $queryBuilder, string $column, $values, int $dataType): void {
		if(is_scalar($values)) {
			$values = [$values];
		}

		if(!is_array($values)) {
			return;
		}

		$parameter = 'values_' . str_replace('.', '_', $column);
		$queryBuilder->andWhere(
			$queryBuilder->expr()->in($column, ':' . $parameter)
    );

		$queryBuilder->setParameter($parameter, $values, $dataType);
	}

	public function getFilterComponent(SqlFilterDefinitionInterface $filterDefinition, EntitySchema $schema, string $filterKey = ''): array {
		return [
			'component' => 'InFilter',
      'type' => $filterDefinition->getType(),
			'name' => $filterDefinition->getKey(),
			'title' => $filterDefinition->getTitle(),
			'description' => $filterDefinition->getDescription(),
			'defaultValue' => $filterDefinition->getDefaultValue(),
		];
	}
}
