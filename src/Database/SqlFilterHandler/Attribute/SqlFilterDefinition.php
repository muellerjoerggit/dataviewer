<?php

namespace App\Database\SqlFilterHandler\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
class SqlFilterDefinition extends AbstractSqlFilterDefinition implements SqlFilterDefinitionInterface  {

  public function __construct(
    string $filterHandler,
    string $key = '',
    string $title = 'Filter',
    string $description = '',
    bool $group = true,
    string $groupKey = ''
  ) {
    parent::__construct($filterHandler, $key, $title, $description, $group, $groupKey);
  }

}