<?php

namespace App\DaViEntity\Schema\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class DatabaseAttr {

  public function __construct(
    public readonly string $databaseClass,
    public readonly string $baseTable,
  ) {}

  public function isValid(): bool {
    return !empty($this->databaseClass) && !empty($this->baseTable);
  }

}