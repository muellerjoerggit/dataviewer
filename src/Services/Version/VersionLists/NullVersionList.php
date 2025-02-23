<?php

namespace App\Services\Version\VersionLists;

use App\Services\Version\VersionListInterface;

class NullVersionList implements VersionListInterface {

  public function getKey(): string {
    return 'nullVersionList';
  }

  public function hasVersion(string $version): bool {
    return false;
  }

}