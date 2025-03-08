<?php

namespace App\Services\Version;

use App\Services\Version\VersionLists\AllVersionList;
use App\Services\Version\VersionLists\NullVersionList;
use App\Services\Version\VersionLists\VersionList;
use App\SymfonyRepository\VersionRepository;

class VersionService {

  private array $versionKeys = [];
  private array $versions = [];
  private array $versionsLists = [];
  private VersionListInterface $nullVersionList;
  private VersionListInterface $allVersionList;

  public function __construct(
    private readonly VersionRepository $versionRepository,
  ) {
    $this->nullVersionList = new NullVersionList();
    $this->allVersionList = new AllVersionList();
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

    return array_slice($this->versionKeys, 0, $index, true);
  }

  public function getVersionSince(string $version): array {
    $index = array_search($version, $this->versionKeys);
    $index = $index ? $index : 0;

    return array_slice($this->versionKeys, $index, null, true);
  }

  public function getVersionList(VersionInformationWrapperInterface $definition): VersionListInterface {
    $versionString = $this->buildVersionString($definition);
    if(isset($this->versionsLists[$versionString])) {
      return $this->versionsLists[$versionString];
    }

    $sinceVersions = [];
    $untilVersions = [];
    foreach ($definition->getVersionInformation() as $key => $version) {
      if(!in_array($key, VersionInformation::VALID_VERSION_INFORMATION_KEYS)) {
        return $this->nullVersionList;
      }

      if(empty($version)) {
        continue;
      }

      switch ($key) {
        case VersionInformation::SINCE_VERSION:
          $sinceVersions = $this->getVersionSince($version);
          break;
        case VersionInformation::UNTIL_VERSION:
          $untilVersions = $this->getAllVersionsUntil($version);
      }
    }

    if(empty($sinceVersions) && empty($untilVersions)) {
      return $this->allVersionList;
    } elseif(empty($untilVersions)) {
      $versions = $sinceVersions;
    } elseif (empty($sinceVersions)) {
      $versions = $untilVersions;
    } else {
      $versions = array_intersect($sinceVersions, $untilVersions);
    }

    $list = new VersionList($versionString, $versions);
    $this->versionsLists[$versionString] = $list;
    return $list;
  }

  public function buildVersionString(VersionInformationWrapperInterface $versionInformation): string {
    $versionString = '';
    foreach ($versionInformation->getVersionInformation() as $key => $version) {
      if(empty($version)) {
        break;
      }
      $versionString .= empty($versionString) ? $key . '_' .  $version : '_' . $key . '_' .  $version ;
    }
    return $versionString;
  }
}