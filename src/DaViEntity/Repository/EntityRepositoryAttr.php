<?php

namespace App\DaViEntity\Repository;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class EntityRepositoryAttr {

  public const string CLASS_PROPERTY = 'entityRepositoryClass';

  public function __construct(
    public readonly string $entityRepositoryClass,
  ) {}

}