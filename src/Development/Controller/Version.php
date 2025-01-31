<?php

namespace App\Development\Controller;

use App\Services\Version\VersionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Version extends AbstractController {

  public function getVersionList(VersionService $versionService) {
    dd($versionService->getVersionList());
  }

  public function getVersionSince(VersionService $versionService) {
    $list = $versionService->getVersionList();
    dd($list->getAllVersionsSince('1.2'));
  }

}