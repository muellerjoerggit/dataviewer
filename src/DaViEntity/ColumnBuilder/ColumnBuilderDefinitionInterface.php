<?php

namespace App\DaViEntity\ColumnBuilder;

use App\Services\Version\VersionInformationWrapperInterface;
use App\Services\Version\VersionListWrapperInterface;

interface ColumnBuilderDefinitionInterface extends VersionInformationWrapperInterface, VersionListWrapperInterface  {

  public function getColumnBuilderClass(): string;

  public function isValid(): bool;

}