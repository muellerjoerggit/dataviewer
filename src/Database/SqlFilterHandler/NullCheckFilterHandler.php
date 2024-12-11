<?php

namespace App\Database\SqlFilterHandler;

use App\Database\DaViQueryBuilder;
use App\Database\SqlFilter\SqlFilterDefinitionInterface;
use App\Database\SqlFilter\SqlFilterInterface;
use App\DaViEntity\Schema\EntitySchema;

class NullCheckFilterHandler extends AbstractFilterHandler {

  public const int OPTION_IS_NULL = 1;

  public const int OPTION_IS_NOT_NULL = 2;

  public const string COMPONENT_NAME = 'SelectSingleFilter';

  public function extendQueryWithFilter(DaViQueryBuilder $queryBuilder, SqlFilterInterface $filter, EntitySchema $schema): void {
    $value = $filter->getValue();
    $column = $this->getColumn($filter, $schema);
    $value = is_array($value) ? reset($value) : $value;

    $expressionBuilder = $queryBuilder->expr();
    $expression = $value === self::OPTION_IS_NOT_NULL ? $expressionBuilder->isNotNull($column) : $expressionBuilder->isNull($column);

    $queryBuilder->andWhere(
      $expression
    );
  }

  public function getFilterComponent(SqlFilterDefinitionInterface $filterDefinition, EntitySchema $schema, string $filterKey = ''): array {
    $component = $this->getFilterComponentInternal($filterDefinition, $schema, $filterKey);
    $possibleValues = [
      [
        'optionId' => self::OPTION_IS_NULL,
        'label' => 'Wert ist NULL',
        'description' => 'Filtert alle Werte, die NULL sind',
      ],
      [
        'optionId' => self::OPTION_IS_NOT_NULL,
        'label' => 'Wert ist nicht NULL',
        'description' => 'Filtert alle Werte, die nicht NULL sind',
      ],
    ];

    $component['additional']['possibleValues'] = $possibleValues;
    return $component;
  }

}