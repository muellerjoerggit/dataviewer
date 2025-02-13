<?php

namespace App\DaViEntity\Validator;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class ValidatorDefinition implements ValidatorDefinitionInterface {

  public function __construct(
    public readonly string $validatorClass,
  ) {}

  public function getValidatorClass(): string {
    return $this->validatorClass;
  }

  public function isValid(): bool {
    return !empty($this->validatorClass);
  }

}