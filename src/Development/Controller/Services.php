<?php

namespace App\Development\Controller;

use App\DaViEntity\Schema\EntityTypesReader;
use App\Services\HtmlService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

class Services extends AbstractController {

  public function entityTypeReader(EntityTypesReader $entityTypesReader): void {
    $entityTypes = $entityTypesReader->read();
    dd($entityTypes);
  }

  public function extractUri(HtmlService $htmlService): void {
    dd($htmlService->extractUris(self::HTML));
  }

  private const string HTML = <<<HTML
<body>
  <div>
    <h2>Hello</h2>
    <p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum.</p>
    <a href="www.example.com">Example</a>
    <img src="www.example.com\example.png" alt="Example">
  </div>
</body>
HTML;
}
