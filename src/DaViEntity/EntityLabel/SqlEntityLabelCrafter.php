<?php

namespace App\DaViEntity\EntityLabel;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class SqlEntityLabelCrafter {

  public const string CLASS_PROPERTY = 'sqlEntityLabelCrafterClass';

  public function __construct(
    public readonly string $sqlEntityLabelCrafterClass,
  ) {}
}