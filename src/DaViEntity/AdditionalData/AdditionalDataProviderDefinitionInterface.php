<?php

namespace App\DaViEntity\AdditionalData;

use App\Services\Version\VersionInformationWrapperInterface;
use App\Services\Version\VersionListWrapperInterface;

interface AdditionalDataProviderDefinitionInterface extends VersionInformationWrapperInterface, VersionListWrapperInterface  {

  public function getAdditionalDataProviderClass(): string;

  public function isValid(): bool;

}