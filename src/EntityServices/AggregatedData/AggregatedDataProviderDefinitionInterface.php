<?php

namespace App\EntityServices\AggregatedData;

use App\Services\Version\VersionInformationWrapperInterface;
use App\Services\Version\VersionListWrapperInterface;

interface AggregatedDataProviderDefinitionInterface extends VersionInformationWrapperInterface, VersionListWrapperInterface {

  public function getAggregatedDataProviderClass(): string;

  public function isValid(): bool;

}