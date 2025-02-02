<?php

namespace App\DaViEntity\ListProvider;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class EntityListProvider {

  public const string CLASS_PROPERTY = 'entityListClass';

  public function __construct(
    public readonly string $entityListClass
  ) {}

}