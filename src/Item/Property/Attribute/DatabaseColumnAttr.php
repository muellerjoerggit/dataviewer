<?php

namespace App\Item\Property\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class DatabaseColumnAttr {

  public function __construct(
    public string $column = '',
    public string $tableReferenceName = '',
  ) {}

  public function setColumn(string $column): static {
    $this->column = $column;
    return $this;
  }

  public function hasColumn(): bool {
    return !empty($this->column);
  }

  public function hasTableReferenceName(): bool {
    return !empty($this->tableReferenceName);
  }

  public function getColumn(): string {
    return $this->column;
  }

  public function getTableReferenceName(): string {
    return $this->tableReferenceName;
  }

}