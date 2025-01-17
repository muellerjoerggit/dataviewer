<?php

namespace App\Database\TableReferenceHandler\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class CommonTableReferenceAttrAttr extends TableReferenceAttr {

  public function __construct(
    string $name,
    string $handlerClass,
    public readonly string $entityType,
    public readonly array $propertyConditions,
    public readonly array $staticConditions = [],
  ) {
    parent::__construct($name, $handlerClass);
  }

}