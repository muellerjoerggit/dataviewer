<?php

namespace App\Services\Version;

interface VersionListWrapperInterface {

  public function hasVersion(string $version): bool;

  public function setVersionList(VersionListInterface $versionList);

}