<?php

namespace App\DaViEntity\EntityListSearch;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class EntityListSearch {

  public const string CLASS_PROPERTY = 'entityListSearch';

  public function __construct(
    public readonly string $entityListSearch
  ) {}

  public function getEntityListSearch(): string {
    return $this->entityListSearch;
  }

}