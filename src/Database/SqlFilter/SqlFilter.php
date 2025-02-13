<?php

namespace App\Database\SqlFilter;

use App\Database\SqlFilterHandler\Attribute\SqlFilterDefinitionInterface;

class SqlFilter implements SqlFilterInterface {

  private array $options = [];

  public function __construct(
    private readonly SqlFilterDefinitionInterface $filterDefinition,
    private readonly mixed $value,
    private readonly string $filterKey
  ) {}

  public function getFilterDefinition(): SqlFilterDefinitionInterface {
    return $this->filterDefinition;
  }

  public function getValue(): mixed {
    return $this->value;
  }

  public function getOptions(): array {
    return $this->options;
  }

  public function getFilterKey(): string {
    return $this->filterKey;
  }

}
