<?php

namespace App\DaViEntity\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class EntityType {

  public function __construct(
    public readonly string $name
  ) {}

}