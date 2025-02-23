<?php

namespace App\Database\AggregationHandler\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class CountAggregationHandlerDefinition extends AbstractAggregationDefinition {

  public function __construct(
    string $name,
    string $aggregationHandlerClass,
    string $title = '',
    string $description = '',
    public readonly string $labelCountColumn = 'Anzahl',
  ) {
    parent::__construct($name, $aggregationHandlerClass, $title, $description);
  }

  public function getLabelCountColumn(): string {
    return $this->labelCountColumn;
  }

}