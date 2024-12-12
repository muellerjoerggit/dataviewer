<?php

namespace App\Development\Controller;

use App\DaViEntity\EntityControllerLocator;
use App\DaViEntity\Schema\EntityTypesReader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

class Services extends AbstractController {

  public function locator(EntityControllerLocator $locator): void {
    dd($locator->getProvidedServices());
  }

  public function entityTypeReader(EntityTypesReader $entityTypesReader): void {
    dd($entityTypesReader->read());
  }

}