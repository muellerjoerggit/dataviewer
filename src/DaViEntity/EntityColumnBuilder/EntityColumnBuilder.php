<?php

namespace App\DaViEntity\EntityColumnBuilder;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class EntityColumnBuilder {

  public const string CLASS_PROPERTY = 'entityColumnBuilderClass';

  public function __construct(
    public readonly string $entityColumnBuilderClass,
  ) {}
}