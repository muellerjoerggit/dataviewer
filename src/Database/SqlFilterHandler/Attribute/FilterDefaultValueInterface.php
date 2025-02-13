<?php

namespace App\Database\SqlFilterHandler\Attribute;

interface FilterDefaultValueInterface {

  public function getDefaultValue(): array | string | int | null;

  public function hasDefaultValue(): bool;

}