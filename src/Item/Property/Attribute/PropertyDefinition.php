<?php

namespace App\Item\Property\Attribute;

use App\Item\ItemConfigurationInterface;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class PropertyDefinition extends AbstractPropertyAttribute  {

  public function __construct(
    public readonly int $dataType,
    public readonly string $label = '',
    public readonly string $description = '',
    public readonly int $cardinality = ItemConfigurationInterface::CARDINALITY_SINGLE,
  ) {}

  public function getDataType(): int {
    return $this->dataType;
  }

  public function getLabel(): string {
    return $this->label;
  }

  public function hasDescription(): bool {
    return !empty($this->description);
  }

  public function getDescription(): string {
    return $this->description;
  }

  public function getCardinality(): int {
    return $this->cardinality;
  }



}