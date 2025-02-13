<?php

namespace App\Item\Property\Traits;

trait SchemaMainPropertyTrait {

  public function toArray(): array {
    return [
      'name' => $this->path,
      'label' => $this->label,
      'rank' => $this->rank,
    ];
  }

}