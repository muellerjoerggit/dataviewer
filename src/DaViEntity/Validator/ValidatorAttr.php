<?php

namespace App\DaViEntity\Validator;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class ValidatorAttr {

  public function __construct(
    public readonly string $validatorClass,
  ) {}

}