<?php

namespace App\Database\SqlFilterHandler;

use App\Database\SqlFilter\SqlFilter;
use App\Database\SqlFilter\SqlFilterDefinitionInterface;
use App\Database\SqlFilter\SqlFilterHandlerInterface;
use App\Database\SqlFilter\SqlFilterInterface;
use App\DaViEntity\Schema\EntitySchema;

abstract class AbstractFilterHandler implements SqlFilterHandlerInterface  {

  protected const string COMPONENT_NAME = '';

  public function buildFilterFromApi(SqlFilterDefinitionInterface $filterDefinition, mixed $filterValues, string $filterKey): SqlFilterInterface {
		return new SqlFilter($filterDefinition, $filterValues, $filterKey);
	}

  public function getFilterComponent(SqlFilterDefinitionInterface $filterDefinition, EntitySchema $schema): array {
		return [];
	}

  protected function getFilterComponentInternal(SqlFilterDefinitionInterface $filterDefinition): array {
    $component = $this->getDefaultComponent($filterDefinition);

    if(empty($component)) {
      return [];
    }

    $component['component'] = static::COMPONENT_NAME;
    return $component;
  }

  protected function getDefaultComponent(SqlFilterDefinitionInterface $filterDefinition): array {
    $filterKey = $filterDefinition->getKey();
    $title = $filterDefinition->getTitle();

    return [
      'filterKey' => $filterKey,
      'title' => $title,
      'description' => $filterDefinition->getDescription(),
      'defaultValue' => $filterDefinition->getDefaultValue(),
      'mandatory' => false,
      'additional' => []
    ];
  }

  protected function getColumn(SqlFilterInterface $filter, EntitySchema $schema): string {
    $filterDefinition = $filter->getFilterDefinition();
    $property = $filterDefinition->getProperty();

    return $schema->getColumn($property);
  }

}
