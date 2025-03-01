<?php

namespace App\EntityServices\ViewBuilder;

use App\Services\Version\VersionInformationWrapperInterface;
use App\Services\Version\VersionListWrapperInterface;

interface ViewBuilderDefinitionInterface extends VersionInformationWrapperInterface, VersionListWrapperInterface {

  public function getViewBuilderClass(): string;

  public function isValid(): bool;

}