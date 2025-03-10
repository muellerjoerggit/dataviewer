<?php

namespace App\DaViEntity\Schema\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class EntityTypeDefinition {

  public const string NAME_PROPERTY = 'name';

  public function __construct(
    public readonly string $name,
    public readonly string $label = ''
  ) {}

  public function getName(): string {
    return $this->name;
  }

  public function getLabel(): string {
    return empty($this->label) ? $this->name : $this->label;
  }

}