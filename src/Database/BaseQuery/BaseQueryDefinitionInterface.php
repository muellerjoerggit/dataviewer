<?php

namespace App\Database\BaseQuery;

use App\Services\Version\VersionInformationWrapperInterface;
use App\Services\Version\VersionListWrapperInterface;

interface BaseQueryDefinitionInterface extends VersionInformationWrapperInterface, VersionListWrapperInterface  {

  public function getBaseQueryClass(): string;

  public function isValid(): bool;

}