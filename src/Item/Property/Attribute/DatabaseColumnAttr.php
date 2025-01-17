<?php

namespace App\Item\Property\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class DatabaseColumnAttr {

  public function __construct(
    public string $column = '',
  ) {}

  public function setColumn(string $column): static {
    $this->column = $column;
    return $this;
  }

}