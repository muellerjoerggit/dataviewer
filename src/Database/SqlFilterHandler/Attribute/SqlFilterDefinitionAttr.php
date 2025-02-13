<?php

namespace App\Database\SqlFilterHandler\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
class SqlFilterDefinitionAttr extends AbstractSqlFilterDefinition implements SqlFilterDefinitionInterface  {

  public function __construct(
    string $filterHandler,
    string $title = 'Filter',
    string $description = '',
    bool $group = true,
    string $groupKey = ''
  ) {
    parent::__construct($filterHandler, $title, $description, $group, $groupKey);
  }

}