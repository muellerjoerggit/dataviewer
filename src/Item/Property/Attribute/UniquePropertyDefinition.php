<?php

namespace App\Item\Property\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class UniquePropertyDefinition extends AbstractPropertyAttribute {

  public function __construct(
    public readonly string $name = 'primary'
  ) {}

  public function getName(): string {
    return $this->name;
  }

}