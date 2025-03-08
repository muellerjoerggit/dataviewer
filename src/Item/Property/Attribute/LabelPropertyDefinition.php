<?php

namespace App\Item\Property\Attribute;

use App\DaViEntity\Schema\Attribute\LabelDefinitionInterface;
use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class LabelPropertyDefinition extends AbstractPropertyAttribute implements LabelDefinitionInterface {

  public function __construct(
    public readonly string $label = '',
    public readonly int $rank = 0
  ) {}

  public function getLabel(): string {
    return empty($this->label) ? $this->property : $this->label;
  }

  public function getPath(): string {
    return $this->property;
  }

  public function getRank(): int {
    return $this->rank;
  }

}