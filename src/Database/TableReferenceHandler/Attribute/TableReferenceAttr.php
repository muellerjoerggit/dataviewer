<?php

namespace App\Database\TableReferenceHandler\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class TableReferenceAttr implements TableReferenceAttrInterface {

  public function __construct(
    public readonly string $name,
    public readonly string $handlerClass,
  ) {}

}