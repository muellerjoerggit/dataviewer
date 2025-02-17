<?php

namespace App\DaViEntity\Traits;

use App\Services\Version\VersionInformation;
use App\Services\Version\VersionListInterface;

trait DefinitionVersionTrait {

  public function getVersionInformation(): array {
    return [
      VersionInformation::SINCE_VERSION => $this->sinceVersion,
      VersionInformation::UNTIL_VERSION => $this->untilVersion,
    ];
  }

  public function hasVersion(string $version): bool {
    return $this->versionList->hasVersion($version);
  }

  public function setVersionList(VersionListInterface $versionList): static {
    $this->versionList = $versionList;
    return $this;
  }

}