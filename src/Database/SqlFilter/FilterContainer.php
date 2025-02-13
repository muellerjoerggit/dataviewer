<?php

namespace App\Database\SqlFilter;

use Generator;
use App\Database\SqlFilterHandler\Attribute\SqlFilterDefinitionInterface;

class FilterContainer {

  private array $filters = [];

  private int $limit = 50;

  private string $client = '';

  public function __construct(string $client, array $filters = []) {
    $this->filters = $filters;
    $this->client = $client;
  }

  public function getClient(): string {
    return $this->client;
  }

  public function getFilter(string $filterName): SqlFilterInterface|bool {
    if (!$this->hasFilter($filterName)) {
      return FALSE;
    }
    return $this->filters[$filterName];
  }

  public function hasFilter($key): bool {
    return isset($this->filters[$key]);
  }

  public function hasFilters(): bool {
    return !empty($this->filters);
  }

  public function addFiltersIfNotExists(FilterContainer | array | SqlFilterDefinitionInterface | SqlFilter $filters): FilterContainer {
    if (($filters instanceof SqlFilterInterface || $filters instanceof SqlFilterDefinitionInterface) && !$this->hasFilter($filters->getFilterKey())) {
      $this->addFilters($filters);
      return $this;
    }

    if (is_array($filters)) {
      foreach ($filters as $filter) {
        if (!($filter instanceof SqlFilterInterface) || !($filter instanceof SqlFilterDefinitionInterface)) {
          continue;
        }
        $this->addFiltersIfNotExists($filter);
      }
      return $this;
    }

    if ($filters instanceof FilterContainer) {
      foreach ($filters->iterateFilters() as $filter) {
        if (!($filter instanceof SqlFilterInterface) || !($filter instanceof SqlFilterDefinitionInterface)) {
          continue;
        }
        $this->addFiltersIfNotExists($filter);
      }
      return $this;
    }

    return $this;
  }

  public function addFilters(SqlFilterInterface|SqlFilterDefinitionInterface $filter): void {
    $name = $filter->getFilterKey();

    $this->filters[$name] = $filter;
  }

  public function iterateFilters(): Generator {
    foreach ($this->filters as $key => $filter) {
      yield $key => $filter;
    }
  }

  public function getLimit(): int {
    return $this->limit;
  }

  public function setLimit(int $limit): FilterContainer {
    $this->limit = $limit;
    return $this;
  }

}
