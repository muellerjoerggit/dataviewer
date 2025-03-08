<?php

namespace App\Feature;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class FeatureDefinition {

  public function __construct(
    public string $label = '',
    public string $description = '',
  ) {}

  public function getDescription(): string {
    return $this->description;
  }

  public function getLabel(): string {
    return $this->label;
  }
}