<?php

namespace App\DaViEntity\Validator;

use App\DaViEntity\Traits\DefinitionVersionTrait;
use App\Services\Version\VersionListInterface;
use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class ValidatorDefinition implements ValidatorDefinitionInterface {

  use DefinitionVersionTrait;

  private VersionListInterface $versionList;

  public function __construct(
    public readonly string $validatorClass,
    public readonly string $sinceVersion = '',
    public readonly string $untilVersion = '',
  ) {}

  public function getValidatorClass(): string {
    return $this->validatorClass;
  }

  public function isValid(): bool {
    return !empty($this->validatorClass);
  }

}