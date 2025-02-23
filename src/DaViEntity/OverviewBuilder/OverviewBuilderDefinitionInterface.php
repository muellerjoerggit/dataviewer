<?php

namespace App\DaViEntity\OverviewBuilder;

use App\Services\Version\VersionInformationWrapperInterface;
use App\Services\Version\VersionListWrapperInterface;

interface OverviewBuilderDefinitionInterface extends VersionInformationWrapperInterface, VersionListWrapperInterface {

  public function getOverviewBuilderClass(): string;

  public function isValid(): bool;

}