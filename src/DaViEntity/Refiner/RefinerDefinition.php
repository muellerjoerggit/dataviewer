<?php

namespace App\DaViEntity\Refiner;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class RefinerDefinition implements RefinerDefinitionInterface {

  public function __construct(
    public readonly string $refinerClass,
  ) {}

  public function getRefinerClass(): string {
    return $this->refinerClass;
  }

  public function isValid(): bool {
    return !empty($this->refinerClass);
  }

}