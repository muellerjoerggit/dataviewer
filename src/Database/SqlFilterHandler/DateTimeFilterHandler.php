<?php

namespace App\Database\SqlFilterHandler;

use App\Database\DatabaseInterface;
use App\Database\DaViQueryBuilder;
use App\Database\SqlFilterHandler\Attribute\SqlFilterDefinitionInterface;
use App\Database\SqlFilter\SqlFilterHandlerInterface;
use App\Database\SqlFilter\SqlFilterInterface;
use App\DaViEntity\Schema\EntitySchema;
use DateTime;
use Exception;

class DateTimeFilterHandler extends AbstractFilterHandler implements SqlFilterHandlerInterface {

  protected const string COMPONENT_NAME = 'DateTimeFilter';

	protected function buildWhere(DaViQueryBuilder $queryBuilder, array $filterData, string $column): bool {
		$parameterFrom = 'values_' . str_replace('.', '_', $column) . '_from';
		$fromDateTime = $filterData['fromDateTime'];

		if(!($fromDateTime instanceof DateTime)) {
			return false;
		}

		$fromDateTime = $fromDateTime->format(DatabaseInterface::DB_DATETIME_FORMAT);

		switch ($filterData['filterType']) {
			case DateTimeFilterHandlerInterface::FILTER_TYPE_EXACT:
				$expression = $queryBuilder->expr()->eq($column, ':' . $parameterFrom);
				break;
			case DateTimeFilterHandlerInterface::FILTER_TYPE_BEFORE:
				$expression = $queryBuilder->expr()->lte($column, ':' . $parameterFrom);
				break;
			case DateTimeFilterHandlerInterface::FILTER_TYPE_AFTER:
				$expression = $queryBuilder->expr()->gte($column, ':' . $parameterFrom);
				break;
			case DateTimeFilterHandlerInterface::FILTER_TYPE_BETWEEN:
				$toDateTime = $filterData['toDateTime'];

				if(!($toDateTime instanceof DateTime)) {
					return false;
				}

				$toDateTime = $toDateTime->format(DatabaseInterface::DB_DATETIME_FORMAT);
				$parameterTo = 'values_' . str_replace('.', '_', $column) . '_to';

				$expression = $column . ' BETWEEN :' . $parameterFrom . ' AND :' . $parameterTo;
				$queryBuilder->setParameter($parameterTo, $toDateTime);
		}

		if(!isset($expression)) {
			return false;
		}

		$queryBuilder->setParameter($parameterFrom, $fromDateTime);

		$queryBuilder->andWhere($expression);

		return true;
	}

	public function extendQueryWithFilter(DaViQueryBuilder $queryBuilder, SqlFilterInterface $filter, EntitySchema $schema): void {
		$filterData = $this->getDateTime($filter);

		if(!$filterData) {
			return;
		}

		$column = $this->getColumn($filter, $schema);

    if(empty($column)) {
      return;
    }

		$this->buildWhere($queryBuilder, $filterData, $column);
	}

	protected function getDateTime(SqlFilterInterface $filter): array | bool {
		$value = $filter->getValue();
		$fromDateTime = $value['fromDateTime'] ?? '';
		$toDateTime = $value['toDateTime'] ?? '';
		$filterType = $value['filterType'];

		if(!$this->validateInputValues($fromDateTime, $toDateTime, $filterType)) {
			return false;
		}

		try {
			$fromDateTime = new DateTime($fromDateTime);
			if($filterType === DateTimeFilterHandlerInterface::FILTER_TYPE_BETWEEN) {
				$toDateTime = new DateTime($toDateTime);
			}
		} catch (Exception $exception) {
			return false;
		}

		return [
			'fromDateTime' => $fromDateTime,
			'toDateTime' => $toDateTime,
			'filterType' => $filterType
		];
	}

	protected function validateInputValues(string $fromDateTime, string $toDateTime, int $filterType): bool {
		if(empty($fromDateTime)) {
			return false;
		}

		if(!in_array($filterType, [
			DateTimeFilterHandlerInterface::FILTER_TYPE_EXACT,
			DateTimeFilterHandlerInterface::FILTER_TYPE_BEFORE,
			DateTimeFilterHandlerInterface::FILTER_TYPE_AFTER,
			DateTimeFilterHandlerInterface::FILTER_TYPE_BETWEEN,
		])) {
			return false;
		}

		if($filterType === DateTimeFilterHandlerInterface::FILTER_TYPE_BETWEEN && empty($toDateTime)) {
			return false;
		}

		return true;
	}

  public function getFilterComponent(SqlFilterDefinitionInterface $filterDefinition, EntitySchema $schema): array {
		return $this->getFilterComponentInternal($filterDefinition);
	}

}
