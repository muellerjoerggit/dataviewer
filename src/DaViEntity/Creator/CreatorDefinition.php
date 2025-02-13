<?php

namespace App\DaViEntity\Creator;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class CreatorDefinition implements CreatorDefinitionInterface {

  public const string CLASS_PROPERTY = 'entityCreator';

  public function __construct(
    public readonly string $entityCreator
  ) {}

}