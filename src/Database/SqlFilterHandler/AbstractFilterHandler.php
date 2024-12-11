<?php

namespace App\Database\SqlFilterHandler;

use App\Database\SqlFilter\PropertyProviderInterface;
use App\Database\SqlFilter\SqlFilter;
use App\Database\SqlFilter\SqlFilterDefinition;
use App\Database\SqlFilter\SqlFilterDefinitionInterface;
use App\Database\SqlFilter\SqlFilterHandlerInterface;
use App\Database\SqlFilter\SqlFilterInterface;
use App\Database\SqlFilter\SqlGeneratedFilterDefinition;
use App\DaViEntity\Schema\EntitySchema;

abstract class AbstractFilterHandler implements SqlFilterHandlerInterface {

  protected const string COMPONENT_NAME = '';

  public function buildFilterFromApi(SqlFilterDefinitionInterface $filterDefinition, mixed $filterValues, string $filterKey): SqlFilterInterface {
    return new SqlFilter($filterDefinition, $filterValues, $filterKey);
  }

  public function getFilterComponent(SqlFilterDefinitionInterface $filterDefinition, EntitySchema $schema, string $filterKey = ''): array {
    return [];
  }

  public function getGeneratedFilterComponent(SqlGeneratedFilterDefinition $filterDefinition, EntitySchema $schema, string $property): array {
    return [];
  }

  protected function getFilterComponentInternal(SqlFilterDefinitionInterface $filterDefinition, EntitySchema $schema, string $filterKey = ''): array {
    $component = $this->getDefaultComponent($filterDefinition, $schema, $filterKey);

    if (empty($component)) {
      return [];
    }

    $component['component'] = static::COMPONENT_NAME;
    return $component;
  }

  protected function getDefaultComponent(SqlFilterDefinitionInterface $filterDefinition, EntitySchema $schema, string $filterKey = ''): array {
    if ($filterDefinition instanceof SqlGeneratedFilterDefinition && empty($filterKey)) {
      return [];
    } elseif ($filterDefinition instanceof SqlFilterDefinition) {
      $filterKey = $filterDefinition->getKey();
    }

    $title = $filterDefinition->getTitle();
    if ($filterDefinition instanceof SqlGeneratedFilterDefinition) {
      $title = $this->getGeneratedFilterTitle($title, $schema, $filterKey);
    }

    return [
      'type' => $filterDefinition->getType(),
      'filterKey' => $filterKey,
      'title' => $title,
      'description' => $filterDefinition->getDescription(),
      'defaultValue' => $filterDefinition->getDefaultValue(),
      'mandatory' => FALSE,
      'additional' => [],
    ];
  }

  protected function getGeneratedFilterTitle(string $title, EntitySchema $schema, string $filterKey): string {
    $property = $schema->getGeneratedFilterProperty($filterKey);

    if (empty($property)) {
      return $title;
    }

    $propertyDefinition = $schema->getProperty($property);
    return strtr($title, ['{property}' => $propertyDefinition->getLabel()]);
  }

  protected function getProperty(SqlFilterInterface $filter, EntitySchema $schema): string {
    $filterKey = $filter->getFilterKey();
    return $schema->getGeneratedFilterProperty($filterKey);
  }

  protected function getColumn(SqlFilterInterface $filter, EntitySchema $schema): string {
    $filterDefinition = $filter->getFilterDefinition();
    if (!$filterDefinition instanceof PropertyProviderInterface) {
      $property = $this->getProperty($filter, $schema);
    } else {
      $property = $filterDefinition->getProperty();
    }

    return $schema->getColumn($property);
  }

}
