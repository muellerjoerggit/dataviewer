<?php

namespace App\Services\Version;

class VersionList {

  private array $versionKeys = [];

  public function __construct(
    private readonly array $versions,
  ) {
    $this->versionKeys = array_keys($this->versions);
  }

  public function getAllVersionsSince(string $version): array {
    $index = array_search($version, $this->versionKeys);
    $index = $index ? $index : 0;

    return array_slice($this->versionKeys, $index, null, true);
  }

  public function getAllVersionsUntil(string $version): array {
    $index = array_search($version, $this->versionKeys);
    $index = $index ? $index + 1 : null;

    return array_slice($this->versionKeys, 0, $index, true);
  }

  public function getAllVersionsBetween(string $versionBegin, string $versionEnd): array {
    $begin = array_search($versionBegin, $this->versionKeys) ?? 0;
    $end = array_search($versionEnd, $this->versionKeys) ?? null;

    return array_slice($this->versionKeys, $begin, $end + 1, true);
  }

  public function hasVersion(string $version): bool {
    return in_array($version, $this->versionKeys);
  }

}