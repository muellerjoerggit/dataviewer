<?php

namespace App\Services\Version;

use App\SymfonyRepository\VersionRepository;

class VersionService {

  public const string YAML_PARAM_VERSION = 'version';
  public const string YAML_PARAM_SINCE_VERSION = 'sinceVersion';

  private array $versionKeys = [];
  private array $versions = [];

  public function __construct(
    private readonly VersionRepository $versionRepository,
  ) {
    $this->init();
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

    $this->versions = $list;
    $this->versionKeys = array_keys($list);
  }

  public function getAllVersionsUntil(string $version): array {
    $index = array_search($version, $this->versionKeys);
    $index = $index ? $index + 1 : null;

    return array_slice($this->versions, 0, $index, true);
  }

  public function getAllVersionsBetween(string $versionBegin, string $versionEnd): array {
    $begin = array_search($versionBegin, $this->versionKeys) ?? 0;
    $end = array_search($versionEnd, $this->versionKeys) ?? null;

    return array_slice($this->versions, $begin, $end + 1, true);
  }

  public function getVersionSince(string $version): array {
    $index = array_search($version, $this->versionKeys);
    $index = $index ? $index : 0;

    return array_slice($this->versions, $index, null, true);
  }
}