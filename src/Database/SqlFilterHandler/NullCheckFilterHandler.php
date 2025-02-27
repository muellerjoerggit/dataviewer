<?php

namespace App\Database\SqlFilterHandler;

use App\Database\QueryBuilder\QueryBuilderInterface;
use App\Database\SqlFilter\SqlFilterInterface;
use App\Database\SqlFilterHandler\Attribute\SqlFilterDefinitionInterface;
use App\DaViEntity\Schema\EntitySchema;

class NullCheckFilterHandler extends AbstractFilterHandler {

  public const int OPTION_IS_NULL = 1;
  public const int OPTION_IS_NOT_NULL = 2;

  public const string COMPONENT_NAME = 'SelectSingleFilter';

  public function extendQueryWithFilter(QueryBuilderInterface $queryBuilder, SqlFilterInterface $filter, EntitySchema $schema): void {
    $value = $filter->getValue();
    $column = $this->getColumn($filter, $schema);
    $value = is_array($value) ? reset($value) : $value;

    $expressionBuilder = $queryBuilder->expr();
    $expression = $value === self::OPTION_IS_NOT_NULL ? $expressionBuilder->isNotNull($column) : $expressionBuilder->isNull($column);

    $queryBuilder->andWhere(
      $expression
    );
  }

  public function getFilterComponent(SqlFilterDefinitionInterface $filterDefinition, EntitySchema $schema): array {
    $component = $this->getFilterComponentInternal($filterDefinition);
    $possibleValues = [[
      'optionId' => self::OPTION_IS_NULL,
      'label' => 'Wert ist NULL',
      'description' => 'Filtert alle Werte, die NULL sind'
    ],[
      'optionId' => self::OPTION_IS_NOT_NULL,
      'label' => 'Wert ist nicht NULL',
      'description' => 'Filtert alle Werte, die nicht NULL sind'
    ]];

    $component['additional']['possibleValues'] = $possibleValues;
    return $component;
  }

}