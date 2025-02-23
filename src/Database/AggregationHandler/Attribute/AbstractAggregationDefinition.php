<?php

namespace App\Database\AggregationHandler\Attribute;

class AbstractAggregationDefinition implements AggregationDefinitionInterface {

  public function __construct(
    public readonly string $name,
    public readonly string $aggregationHandlerClass,
    public readonly string $title = '',
    public readonly string $description = '',
  ) {}

  public function getName(): string {
    return $this->name;
  }

  public function getAggregationHandlerClass(): string {
    return $this->aggregationHandlerClass;
  }

  public function getTitle(): string {
    return $this->title ?? $this->getName();
  }

  public function getDescription(): string {
    return $this->description;
  }

  public function isValid(): bool {
    return !empty($this->aggregationHandlerClass) && !empty($this->name);
  }

}