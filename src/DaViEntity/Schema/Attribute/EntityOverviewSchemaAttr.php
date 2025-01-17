<?php

namespace App\DaViEntity\Schema\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class EntityOverviewSchemaAttr {

  public function __construct(
    public readonly string $path,
    public readonly string $label = '',
    public readonly int $rank = 0
  ) {}

}