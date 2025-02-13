<?php

namespace App\DaViEntity\Schema\Attribute;

use App\Item\Property\Traits\SchemaMainPropertyTrait;

abstract class AbstractOverviewDefinition {

  use SchemaMainPropertyTrait;

  public function __construct(
    public readonly string $path,
    public readonly string $label = '',
    public readonly int $rank = 0
  ) {}

  public function getLabel(): string {
    return $this->label;
  }

  public function getPath(): string {
    return $this->path;
  }

  public function getRank(): int {
    return $this->rank;
  }

}