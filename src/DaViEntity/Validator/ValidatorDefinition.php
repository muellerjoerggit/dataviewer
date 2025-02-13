<?php

namespace App\DaViEntity\Validator;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class ValidatorDefinition implements ValidatorDefinitionInterface {

  public function __construct(
    public readonly string $validatorClass,
  ) {}

}