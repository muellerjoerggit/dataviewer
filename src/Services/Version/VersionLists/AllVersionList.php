<?php

namespace App\Services\Version\VersionLists;

use App\Services\Version\VersionListInterface;

class AllVersionList implements VersionListInterface {

  public function getKey(): string {
    return 'allVersions';
  }

  public function hasVersion(string $version): bool {
    return true;
  }

}