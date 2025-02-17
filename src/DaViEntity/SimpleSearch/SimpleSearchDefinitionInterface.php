<?php

namespace App\DaViEntity\SimpleSearch;

use App\Services\Version\VersionInformationWrapperInterface;
use App\Services\Version\VersionListWrapperInterface;

interface SimpleSearchDefinitionInterface  extends VersionInformationWrapperInterface, VersionListWrapperInterface  {

  public function getSimpleSearchClass(): string;

  public function isValid(): bool;

}