<?php

namespace App\Services\Version;

interface VersionListInterface {

  public function hasVersion(string $version): bool;

}