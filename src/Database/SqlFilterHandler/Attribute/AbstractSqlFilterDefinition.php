<?php

namespace App\Database\SqlFilterHandler\Attribute;

use App\Services\AppNamespaces;

class AbstractSqlFilterDefinition implements SqlFilterDefinitionInterface {

  private readonly string $property;

  public function __construct(
    public readonly string $filterHandler,
    public readonly string $key = '',
    public readonly string $title = 'Filter',
    public readonly string $description = '',
    public readonly bool $group = true,
    public readonly string $groupKey = '',
  ) {}

  public function getProperty(): string {
    return $this->property;
  }

  public function getKey(): string {
    return empty($this->key) ? $this->property . '_' . AppNamespaces::getShortName($this->filterHandler) : $this->key;
  }

  public function setProperty(string $property): static {
    $this->property = $property;
    return $this;
  }

  public function getFilterHandler(): string {
    return $this->filterHandler;
  }

  public function getTitle(): string {
    return $this->title;
  }

  public function getDescription(): string {
    return $this->description;
  }

  public function isGroup(): bool {
    return $this->group;
  }

  public function isValid(): bool {
    return !empty($this->property) && !empty($this->filterHandler);
  }

  public function hasGroupKey(): bool {
    return !empty($this->groupKey);
  }

  public function getGroupKey(): string {
    return $this->groupKey;
  }

}