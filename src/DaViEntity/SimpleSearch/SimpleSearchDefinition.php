<?php

namespace App\DaViEntity\SimpleSearch;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class SimpleSearchDefinition implements SimpleSearchDefinitionInterface {

  public function __construct(
    public readonly string $simpleSearchClass
  ) {}

  public function getSimpleSearchClass(): string {
    return $this->simpleSearchClass;
  }

  public function isValid(): bool {
    return !empty($this->simpleSearchClass);
  }

}