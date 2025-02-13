<?php

namespace App\Item\Property\Attribute;

use App\DaViEntity\Schema\Attribute\EntityOverviewDefinitionInterface;
use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class EntityOverviewPropertyAttr extends AbstractPropertyAttribute implements EntityOverviewDefinitionInterface {

  public function __construct(
    public readonly string $label = '',
    public readonly int $rank = 0,
  ) {}

  public function getLabel(): string {
    return $this->label;
  }

  public function getPath(): string {
    return $this->property;
  }

  public function getRank(): int {
    return $this->rank;
  }

}