<?php

namespace App\Services\Version;

use App\SymfonyRepository\VersionRepository;

class VersionService {

  public const string YAML_PARAM_VERSION = 'version';
  public const string YAML_PARAM_SINCE_VERSION = 'sinceVersion';

  private array $versionList = [];

  public function __construct(
    private readonly VersionRepository $versionRepository,
  ) {
    $this->init();
  }

  public function getVersionListSince(string $sinceVersionId): array {
    $ret = [];
    $start = false;
    foreach ($this->versionList as $version) {
      if ($sinceVersionId === $version->getId()) {
        $start = true;
      }

      if($start) {
        $ret[] = $version;
      }
    }

    return $ret;
  }

  private function init(): void {
    $versions = $this->versionRepository->findAll();

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
      $this->versionList[$successor->getId()] = $successor;
      $successor = $successor->getSuccessor();
      if($successor === null || isset($this->versionList[$successor->getId()])) {
        break;
      }
    } while (true);
  }

}