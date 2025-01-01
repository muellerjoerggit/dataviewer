<?php

namespace App\Tests;

use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use App\DaViEntity\Schema\EntityTypesRegister;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class EntityTypesTest extends KernelTestCase {

  public function testEntityTypeList(): void {
    $kernel = self::bootKernel();

    $this->assertSame('test', $kernel->getEnvironment());

    $entityTypesRegister = static::getContainer()->get(EntityTypesRegister::class);
    $schemaRegister = static::getContainer()->get(EntityTypeSchemaRegister::class);

    $entityTypes = [];
    foreach($entityTypesRegister->iterateEntityTypes() as $entityType) {
      $schema = $schemaRegister->getEntityTypeSchema($entityType);
      $entityType = [
        'type' => $entityType,
        'label' => $schema->getEntityLabel()
      ];

      $entityTypes[] = $entityType;
    }

    $this->assertNotEmpty($entityTypes);
  }


}
