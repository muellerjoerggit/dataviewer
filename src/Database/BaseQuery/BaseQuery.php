<?php

namespace App\Database\BaseQuery;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class BaseQuery {

  public const string CLASS_PROPERTY = 'baseQuery';

  public function __construct(
    public readonly string $baseQuery
  ) {}

  public function getBaseQuery(): string {
    return $this->baseQuery;
  }

}