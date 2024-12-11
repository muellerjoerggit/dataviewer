<?php

namespace App\Database\SqlFilterHandler;

use App\Database\DatabaseInterface;
use App\Database\DaViQueryBuilder;
use App\Database\SqlFilter\SqlFilterDefinitionInterface;
use App\Database\SqlFilter\SqlFilterHandlerInterface;
use App\Database\SqlFilter\SqlFilterInterface;
use App\DaViEntity\Schema\EntitySchema;
use DateTime;
use Exception;

class DateTimeFilterHandler extends AbstractFilterHandler implements SqlFilterHandlerInterface {

  protected const string COMPONENT_NAME = 'DateTimeFilter';

  public function extendQueryWithFilter(DaViQueryBuilder $queryBuilder, SqlFilterInterface $filter, EntitySchema $schema): void {
    $filterData = $this->getDateTime($filter);

    if (!$filterData) {
      return;
    }

    $column = $this->getColumn($filter, $schema);

    if (empty($column)) {
      return;
    }

    $this->buildWhere($queryBuilder, $filterData, $column);
  }

  protected function getDateTime(SqlFilterInterface $filter): array|bool {
    $value = $filter->getValue();
    $fromDateTime = $value['fromDateTime'] ?? '';
    $toDateTime = $value['toDateTime'] ?? '';
    $filterType = $value['filterType'];

    if (!$this->validateInputValues($fromDateTime, $toDateTime, $filterType)) {
      return FALSE;
    }

    try {
      $fromDateTime = new DateTime($fromDateTime);
      if ($filterType === DateTimeFilterHandlerInterface::FILTER_TYPE_BETWEEN) {
        $toDateTime = new DateTime($toDateTime);
      }
    } catch (Exception $exception) {
      return FALSE;
    }

    return [
      'fromDateTime' => $fromDateTime,
      'toDateTime' => $toDateTime,
      'filterType' => $filterType,
    ];
  }

  protected function validateInputValues(string $fromDateTime, string $toDateTime, int $filterType): bool {
    if (empty($fromDateTime)) {
      return FALSE;
    }

    if (!in_array($filterType, [
      DateTimeFilterHandlerInterface::FILTER_TYPE_EXACT,
      DateTimeFilterHandlerInterface::FILTER_TYPE_BEFORE,
      DateTimeFilterHandlerInterface::FILTER_TYPE_AFTER,
      DateTimeFilterHandlerInterface::FILTER_TYPE_BETWEEN,
    ])) {
      return FALSE;
    }

    if ($filterType === DateTimeFilterHandlerInterface::FILTER_TYPE_BETWEEN && empty($toDateTime)) {
      return FALSE;
    }

    return TRUE;
  }

  protected function buildWhere(DaViQueryBuilder $queryBuilder, array $filterData, string $column): bool {
    $parameterFrom = 'values_' . str_replace('.', '_', $column) . '_from';
    $fromDateTime = $filterData['fromDateTime'];

    if (!($fromDateTime instanceof DateTime)) {
      return FALSE;
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

        if (!($toDateTime instanceof DateTime)) {
          return FALSE;
        }

        $toDateTime = $toDateTime->format(DatabaseInterface::DB_DATETIME_FORMAT);
        $parameterTo = 'values_' . str_replace('.', '_', $column) . '_to';

        $expression = $column . ' BETWEEN :' . $parameterFrom . ' AND :' . $parameterTo;
        $queryBuilder->setParameter($parameterTo, $toDateTime);
    }

    if (!isset($expression)) {
      return FALSE;
    }

    $queryBuilder->setParameter($parameterFrom, $fromDateTime);

    $queryBuilder->andWhere($expression);

    return TRUE;
  }

  public function getFilterComponent(SqlFilterDefinitionInterface $filterDefinition, EntitySchema $schema, string $filterKey = ''): array {
    return [
      'component' => 'DateTimeFilter',
      'type' => $filterDefinition->getType(),
      'name' => $filterDefinition->getKey(),
      'title' => $filterDefinition->getTitle(),
      'description' => $filterDefinition->getDescription(),
      'defaultValue' => $filterDefinition->getDefaultValue(),
    ];
  }

}
