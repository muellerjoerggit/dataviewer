<?php

namespace App\Item\ItemHandler_AdditionalData\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class AggregationAdditionalDataHandlerDefinition extends AbstractAdditionalDataHandlerDefinition {

  public function __construct(
    string $handlerClass,
    public readonly string $targetEntityClass,
    public readonly string $aggregationKey,
    public readonly array $filters = [],
    public readonly array $propertyBlacklist = [],
  ) {
    parent::__construct($handlerClass);
  }

  public function getTargetEntityClass(): string {
    return $this->targetEntityClass;
  }

  public function getAggregationKey(): string {
    return $this->aggregationKey;
  }

  public function getFilters(): array {
    return $this->filters;
  }

  public function hasPropertyBlacklist(): bool {
    return !empty($this->propertyBlacklist);
  }

  public function getPropertyBlacklist(): array {
    return $this->propertyBlacklist;
  }

  public function isValid(): bool {
    return parent::isValid()
      && !empty($this->targetEntityClass)
      && !empty($this->aggregationKey);
  }

}