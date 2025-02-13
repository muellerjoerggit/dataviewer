<?php

namespace App\DaViEntity\Repository;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class RepositoryDefinition implements RepositoryDefinitionInterface {

  public const string CLASS_PROPERTY = 'entityRepositoryClass';

  public function __construct(
    public readonly string $entityRepositoryClass,
  ) {}

}