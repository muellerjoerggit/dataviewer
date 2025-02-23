<?php

namespace App\Services\Version;

interface VersionListInterface {

  public function getKey(): string;

  public function hasVersion(string $version): bool;

}