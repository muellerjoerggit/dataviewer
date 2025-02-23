<?php

namespace App\EntityServices\EntityLabel;

use App\Services\Version\VersionInformationWrapperInterface;
use App\Services\Version\VersionListWrapperInterface;

interface LabelCrafterDefinitionInterface extends VersionInformationWrapperInterface, VersionListWrapperInterface {

  public function getLabelCrafterClass(): string;

  public function isValid(): bool;

}