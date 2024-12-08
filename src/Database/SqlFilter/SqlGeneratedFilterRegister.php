<?php

namespace App\Database\SqlFilter;

class SqlGeneratedFilterRegister {

  private array $filters = [];

  public function addFilter(SqlGeneratedFilterDefinition $filterDefinition): void {
    $name = $filterDefinition->getKey();
    $this->filters[$name] = $filterDefinition;
  }

  public function hasFilter(string $hashName): bool {
    return isset($this->filters[$hashName]);
  }

  public function getFilter(string $hashName): SqlGeneratedFilterDefinition {
    return $this->filters[$hashName];
  }

}