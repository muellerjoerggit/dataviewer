<?php

namespace App\DaViEntity\EntityRepository;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class EntityRepository {

  public const string CLASS_PROPERTY = 'entityRepositoryClass';

  public function __construct(
    public readonly string $entityRepositoryClass,
  ) {}

}