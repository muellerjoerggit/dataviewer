<?php

namespace App\DaViEntity\Creator;

use App\Services\Version\VersionInformationWrapperInterface;
use App\Services\Version\VersionListWrapperInterface;

interface CreatorDefinitionInterface extends VersionInformationWrapperInterface, VersionListWrapperInterface  {

  public function getCreatorClass(): string;

  public function isValid(): bool;

}