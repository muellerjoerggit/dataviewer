<?php

namespace App\DaViEntity\Validator;

interface ValidatorDefinitionInterface {

  public function getValidatorClass(): string;

  public function isValid(): bool;

}