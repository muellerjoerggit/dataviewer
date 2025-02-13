<?php

namespace App\DaViEntity\ColumnBuilder;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class ColumnBuilderDefinition implements ColumnBuilderDefinitionInterface {

  public function __construct(
    public readonly string $entityColumnBuilderClass,
  ) {}

  public function getEntityColumnBuilderClass(): string {
    return $this->entityColumnBuilderClass;
  }
}