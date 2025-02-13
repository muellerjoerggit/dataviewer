<?php

namespace App\DaViEntity\ColumnBuilder;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class ColumnBuilder {

  public const string CLASS_PROPERTY = 'entityColumnBuilderClass';

  public function __construct(
    public readonly string $entityColumnBuilderClass,
  ) {}
}