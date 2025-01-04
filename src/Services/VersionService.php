<?php

namespace App\Services;

use App\SymfonyRepository\VersionRepository;

class VersionService {

  public function __construct(
    private readonly VersionRepository $versionRepository,
  ) {
    $this->init();
  }

  private function init(): void {
    $versions = $this->versionRepository->findAll();

  }

}