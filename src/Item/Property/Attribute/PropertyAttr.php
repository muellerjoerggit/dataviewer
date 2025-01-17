<?php

namespace App\Item\Property\Attribute;

use App\Item\ItemConfigurationInterface;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class PropertyAttr {

  private readonly string $name;

  public function __construct(
    public readonly int $dataType,
    public readonly string $label = '',
    public readonly string $description = '',
    public readonly int $cardinality = ItemConfigurationInterface::CARDINALITY_SINGLE,
  ) {}

  public function setName(string $name): static {
    $this->name = $name;
    return $this;
  }

  public function getName(): string {
    return $this->name;
  }

  public function getDataType(): int {
    return $this->dataType;
  }

  public function getLabel(): string {
    return $this->label;
  }

  public function getDescription(): string {
    return $this->description;
  }

  public function getCardinality(): int {
    return $this->cardinality;
  }



}