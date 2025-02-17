<?php

namespace App\Development\Controller;

use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EntityTypes extends AbstractController {

  public function getEntityTypes(EntityTypeSchemaRegister $schemaRegister): void {
    dd($schemaRegister->getEntityTypeSchema('User'));
  }

}