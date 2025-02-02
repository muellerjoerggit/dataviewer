<?php

namespace App\Item\Property\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class LabelPropertyAttr extends AbstractPropertyAttribute {

  public function __construct(
    public readonly string $label = '',
    public readonly int $rank = 0
  ) {}

}