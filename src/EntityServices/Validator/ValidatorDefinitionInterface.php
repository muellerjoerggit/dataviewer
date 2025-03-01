<?php

namespace App\EntityServices\Validator;

use App\Services\Version\VersionInformationWrapperInterface;
use App\Services\Version\VersionListWrapperInterface;

interface ValidatorDefinitionInterface extends VersionInformationWrapperInterface, VersionListWrapperInterface {

  public function getValidatorClass(): string;

  public function isValid(): bool;

}