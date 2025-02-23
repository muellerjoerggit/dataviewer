<?php

namespace App\Database\AggregationHandler\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class CountGroupAggregationHandlerDefinition extends AbstractAggregationDefinition {

  public function __construct(
    string $name,
    string $aggregationHandlerClass,
    public readonly array $header,
    public readonly array $properties,
    public readonly string $labelCountColumn = 'Anzahl',
    string $title = '',
    string $description = '',
  ) {
    parent::__construct($name, $aggregationHandlerClass, $title, $description);
  }

  public function getHeader(): array {
    return $this->header;
  }

  public function getProperties(): array {
    return $this->properties;
  }

  public function getLabelCountColumn(): string {
    return $this->labelCountColumn;
  }

}