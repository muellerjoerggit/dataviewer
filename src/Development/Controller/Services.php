<?php

namespace App\Development\Controller;

use App\DaViEntity\EntityControllerLocator;
use App\DaViEntity\Schema\EntityTypesReader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/debugging', condition: 'env("APP_ENV") === "dev"')]
class Services extends AbstractController {

  #[Route(path: '/locator', name: 'debugging_locator')]
  public function locator(EntityControllerLocator $locator): void {
    dd($locator->getProvidedServices());
  }

  #[Route(path: '/entityTypeReader', name: 'debugging_entity_type_reader')]
  public function entityTypeReader(EntityTypesReader $entityTypesReader): void {
    dd($entityTypesReader->read());
  }

}