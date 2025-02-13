<?php

namespace App\DaViEntity\Search;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class SearchDefinition implements SearchDefinitionInterface {

  public const string CLASS_PROPERTY = 'entityListSearch';

  public function __construct(
    public readonly string $entityListSearch
  ) {}

  public function getEntityListSearch(): string {
    return $this->entityListSearch;
  }

}