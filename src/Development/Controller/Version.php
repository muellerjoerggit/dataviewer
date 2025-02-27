<?php

namespace App\Development\Controller;

use App\Services\Version\VersionService;
use App\SymfonyRepository\VersionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Version extends AbstractController {

  public function getVersionService(VersionService $versionService) {
    dd($versionService);
  }

  public function getVersions(VersionRepository $versionRepository) {
    dd($versionRepository->findAll());
  }

}