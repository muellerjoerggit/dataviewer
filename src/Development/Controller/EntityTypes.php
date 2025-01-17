<?php

namespace App\Development\Controller;

use App\DaViEntity\EntityControllerLocator;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use App\DaViEntity\Schema\EntityTypesReader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EntityTypes extends AbstractController {

  public function getEntityTypes(EntityTypeSchemaRegister $schemaRegister): void {
    dd($schemaRegister->getEntityTypeSchema('Role'));
  }

}