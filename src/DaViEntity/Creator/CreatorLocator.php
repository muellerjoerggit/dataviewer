<?php

namespace App\DaViEntity\Creator;

use App\DaViEntity\Schema\EntitySchema;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use App\Services\AbstractLocator;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

class CreatorLocator extends AbstractLocator {

  public function __construct(
    private readonly EntityTypeSchemaRegister $entityTypeSchemaRegister,
    #[AutowireLocator('entity_management.entity_creator')]
    ServiceLocator $services,
  ) {
    parent::__construct($services);
  }

  public function getEntityCreator(string | EntitySchema $entitySchema, string $version): CreatorInterface {
    if(is_string($entitySchema)) {
      $entitySchema = $this->entityTypeSchemaRegister->getSchemaFromEntityClass($entitySchema);
    }
    $class = $entitySchema->getCreatorClass($version);

    if($this->has($class)) {
      return $this->get($class);
    } else {
      return $this->get(CommonCreator::class);
    }


  }

}