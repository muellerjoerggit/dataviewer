<?php

namespace App\EntityServices\Refiner;

use App\Services\Version\VersionInformationWrapperInterface;
use App\Services\Version\VersionListWrapperInterface;

interface RefinerDefinitionInterface extends VersionInformationWrapperInterface, VersionListWrapperInterface {

  public function getRefinerClass(): string;

  public function isValid(): bool;

}