<?php

namespace App\Database\SqlFilter;

use App\Database\DaViQueryBuilder;
use App\DaViEntity\Schema\EntitySchema;

class SqlFilterBuilder {

  public function __construct(
    private readonly SqlFilterHandlerLocator $filterHandlerLocator
  ) {}

  public static function buildDefaultFilterContainerAndAppend(string $client, EntitySchema $schema, ?FilterContainer $filters = NULL): FilterContainer {
    $defaultFilters = $schema->getDefaultFiltersAsContainer($client);
    if ($filters instanceof FilterContainer) {
      return $filters->addFiltersIfNotExists($defaultFilters);
    }

    return $defaultFilters;
  }

  public static function buildMandatoryFilterContainerAndAppend(string $client, EntitySchema $schema, ?FilterContainer $filters = NULL): FilterContainer {
    $mandatoryFilters = $schema->getMandatoryFiltersAsContainer($client);
    if ($filters instanceof FilterContainer) {
      return $mandatoryFilters->addFiltersIfNotExists($filters);
    }

    return $mandatoryFilters;
  }

  public function buildFilteredQueryMultipleFilters(DaViQueryBuilder $queryBuilder, FilterContainer $filters, EntitySchema $schema): void {
    foreach ($filters->iterateFilters() as $filter) {
      if ($filter instanceof SqlFilterDefinitionInterface && $filter->hasDefaultValue()) {
        $filter = new SqlFilter($filter, $filter->getDefaultValue());
      }

      if ($filter instanceof SqlFilter) {
        $this->buildFilteredQuery($queryBuilder, $filter, $schema);
      }
    }
  }

  public function buildFilteredQuery(DaViQueryBuilder $queryBuilder, SqlFilter $filter, EntitySchema $schema): void {
    $handler = $this->filterHandlerLocator->getFilterHandlerFromFilterDefinition($filter->getFilterDefinition());

    $handler->extendQueryWithFilter($queryBuilder, $filter, $schema);
  }

  public function buildFilterContainerFromArray(string $client, EntitySchema $schema, array $filters): FilterContainer {
    $filterContainer = new FilterContainer($client);

    foreach ($filters as $filter) {
      $filterKey = $filter['filterKey'] ?? '';
      $filterValues = $filter['filterValues'] ?? [];
      $filterValues = is_array($filterValues) ? $filterValues : [$filterValues];
      if (empty($filterKey) || !$schema->hasFilter($filterKey)) {
        continue;
      } else {
        $filterDefinition = $schema->getFilterDefinition($filterKey);
        $entityFilter = $this->buildFilter($filterDefinition, $filterValues, $filterKey);
        $filterContainer->addFiltersIfNotExists($entityFilter);
      }
    }

    return $filterContainer;
  }

  private function buildFilter(SqlFilterDefinitionInterface $filterDefinition, array $filterValues, string $filterKey): SqlFilter {
    $handler = $this->filterHandlerLocator->getFilterHandlerFromFilterDefinition($filterDefinition);
    return $handler->buildFilterFromApi($filterDefinition, $filterValues, $filterKey);
  }

}
