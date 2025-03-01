<?php

namespace App\EntityServices\ListProvider;

use App\Services\Version\VersionInformationWrapperInterface;
use App\Services\Version\VersionListWrapperInterface;

interface ListProviderDefinitionInterface extends VersionInformationWrapperInterface, VersionListWrapperInterface  {

  public function getListProviderClass(): string;

  public function isValid(): bool;

}