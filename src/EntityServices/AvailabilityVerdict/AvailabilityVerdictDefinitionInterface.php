<?php

namespace App\EntityServices\AvailabilityVerdict;

use App\Services\Version\VersionInformationWrapperInterface;
use App\Services\Version\VersionListWrapperInterface;

interface AvailabilityVerdictDefinitionInterface extends VersionInformationWrapperInterface, VersionListWrapperInterface {

  public function getAvailabilityVerdictServiceClass(): string;

  public function isValid(): bool;

}