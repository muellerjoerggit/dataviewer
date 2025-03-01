<?php

namespace App\EntityServices\Repository;

use App\Services\Version\VersionInformationWrapperInterface;
use App\Services\Version\VersionListWrapperInterface;

interface RepositoryDefinitionInterface extends VersionInformationWrapperInterface, VersionListWrapperInterface  {

  public function getRepositoryClass(): string;

  public function isValid(): bool;

}