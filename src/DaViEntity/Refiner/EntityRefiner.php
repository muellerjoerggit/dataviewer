<?php

namespace App\DaViEntity\Refiner;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class EntityRefiner {

  public const string CLASS_PROPERTY = 'entityRefinerClass';

  public function __construct(
    public readonly string $entityRefinerClass,
  ) {}

}