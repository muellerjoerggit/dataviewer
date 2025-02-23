<?php

namespace App\Services\Version\VersionLists;

use App\Services\Version\VersionListInterface;

class VersionList implements VersionListInterface {

  public function __construct(
    private readonly string $key,
    private readonly array $versions,
  ) {}

  public function getKey(): string {
    return $this->key;
  }

  public function hasVersion(string $version): bool {
    return in_array($version, $this->versions);
  }

}