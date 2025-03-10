<?php

namespace App\Database\SqlFilterHandler;

use App\Database\QueryBuilder\QueryBuilderInterface;
use App\Database\SqlFilter\SqlFilterInterface;
use App\Database\SqlFilter\TextFilterInterface;
use App\Database\SqlFilterHandler\Attribute\SqlFilterDefinitionInterface;
use App\DaViEntity\Schema\EntitySchema;

class CommonTextFilterHandler extends AbstractFilterHandler {

  protected const string COMPONENT_NAME = 'CommonTextFilter';

  public function extendQueryWithFilter(QueryBuilderInterface $queryBuilder, SqlFilterInterface $filter, EntitySchema $schema): void {
    $value = $filter->getValue();
    $column = $this->getColumn($filter, $schema);
    $filterType = $value['filterType'] ?? TextFilterInterface::FILTER_TYPE_CONTAINS;
    $value = $value['value'] ?? '';

    if(empty($column) || (empty($value) && $filterType !== TextFilterInterface::FILTER_TYPE_EMPTY_STRING)) {
      return;
    }

    $this->setWhereTextFilter($queryBuilder, $column, $filterType, $value);
  }

	private function setWhereTextFilter(QueryBuilderInterface $queryBuilder, string $column, string $filterType, $values): void {
		$parameter = 'values_' . str_replace('.', '_', $column);

		switch ($filterType) {
			case TextFilterInterface::FILTER_TYPE_CONTAINS:
				$expression = $queryBuilder->expr()->like($column, ':' . $parameter);
				$values = '%' . $values . '%';
				break;
			case TextFilterInterface::FILTER_TYPE_EMPTY_STRING:
				$values = '';
			case TextFilterInterface::FILTER_TYPE_EQUAL:
				$expression = $queryBuilder->expr()->eq($column, ':' . $parameter);
				break;
			case TextFilterInterface::FILTER_TYPE_ONE_OF_WORDS:
				$expression = $queryBuilder->expr()->comparison($column, 'RLIKE' ,':' . $parameter);
				$values = str_replace(' ', '|', $values);
				break;
			case TextFilterInterface::FILTER_TYPE_CONTAINS_HTML:
				$expression = $queryBuilder->expr()->or(
					$queryBuilder->expr()->like($column, ':' . $parameter),
					$queryBuilder->expr()->like($column, ':' . $parameter . '_html')
				);
				$values = '%' . $values . '%';
				$valuesHtml = '%' . htmlentities($values) . '%';
				$queryBuilder->setParameter($parameter . '_html', $valuesHtml);
				break;
		}

		if(!isset($expression)) {
			return;
		}

		$queryBuilder->andWhere(
			$expression
		);

		$queryBuilder->setParameter($parameter, $values);
	}

  public function getFilterComponent(SqlFilterDefinitionInterface $filterDefinition, EntitySchema $schema): array {
		return $this->getFilterComponentInternal($filterDefinition);
	}

}
