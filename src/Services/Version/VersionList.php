<?php

namespace App\Services\Version;

class VersionList {

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