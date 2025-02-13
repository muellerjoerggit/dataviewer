<?php

namespace App\DaViEntity\Creator;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class CreatorDefinition implements CreatorDefinitionInterface {

  public function __construct(
    public readonly string $creatorClass
  ) {}

  public function getCreatorClass(): string {
    return $this->creatorClass;
  }

  public function isValid(): bool {
    return !empty($this->creatorClass);
  }

}