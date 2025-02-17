<?php

namespace App\Item\Property\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class DatabaseColumnDefinition {

  public function __construct(
    public string $column = '',
  ) {}

  public function setColumn(string $column): static {
    $this->column = $column;
    return $this;
  }

  public function hasColumn(): bool {
    return !empty($this->column);
  }

  public function getColumn(): string {
    return $this->column;
  }

}