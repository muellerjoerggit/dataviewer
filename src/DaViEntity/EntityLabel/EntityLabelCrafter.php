<?php

namespace App\DaViEntity\EntityLabel;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class EntityLabelCrafter {

  public const string CLASS_PROPERTY = 'entityLabelCrafterClass';

  public function __construct(
    public readonly string $entityLabelCrafterClass,
  ) {}
}