<?php

namespace App\Database\TableReference;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class TableReferencePropertyDefinition  {

  public function __construct(
    public readonly string $tableReferenceName,
    public readonly string $property,
  ) {}

  public function getTableReferenceName(): string {
    return $this->tableReferenceName;
  }

  public function getProperty(): string {
    return $this->property;
  }

  public function isValid(): bool {
    return !empty($this->property) && !empty($this->tableReferenceName);
  }

}