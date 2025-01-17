<?php

namespace App\Item\Property\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class UniquePropertyAttr {

  public function __construct(
    public readonly string $name = 'primary'
  ) {}

}