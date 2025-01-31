<?php

namespace App\Services\Version;

use App\SymfonyRepository\VersionRepository;

class VersionService {

  public const string YAML_PARAM_VERSION = 'version';
  public const string YAML_PARAM_SINCE_VERSION = 'sinceVersion';

  private VersionList $versionList;

  public function __construct(
    private readonly VersionRepository $versionRepository,
  ) {
    $this->init();
  }

  public function getVersionList(): VersionList {
    return $this->versionList;
  }

  public function getVersionListSince(string $sinceVersionId): array {
    return $this->versionList->getAllVersionsSince($sinceVersionId);
  }

  private function init(): void {
    $versions = $this->versionRepository->findAll();
    $list = [];

    $successor = null;
    foreach ($versions as $version) {
      if($version->getPredecessor() === null) {
        $successor = $version;
        break;
      }
    }

    if($successor === null) {
      return;
    }

    do {
      $list[$successor->getId()] = $successor;
      $successor = $successor->getSuccessor();
      if($successor === null || isset($list[$successor->getId()])) {
        break;
      }
    } while (true);

    $this->versionList = new VersionList($list);
  }
}