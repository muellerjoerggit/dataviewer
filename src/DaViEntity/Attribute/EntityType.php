<?php

namespace App\DaViEntity\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class EntityType {

  public const string NAME_PROPERTY = 'name';

  public function __construct(
    public readonly string $name,
  ) {}

}