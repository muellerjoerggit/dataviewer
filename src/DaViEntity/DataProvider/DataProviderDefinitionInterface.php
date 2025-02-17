<?php

namespace App\DaViEntity\DataProvider;

use App\Services\Version\VersionInformationWrapperInterface;
use App\Services\Version\VersionListWrapperInterface;

interface DataProviderDefinitionInterface extends VersionInformationWrapperInterface, VersionListWrapperInterface  {

  public function getDataProviderClass(): string;

  public function isValid(): bool;

}