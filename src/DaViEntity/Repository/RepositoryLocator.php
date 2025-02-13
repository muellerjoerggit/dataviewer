<?php

namespace App\DaViEntity\Repository;

use App\DaViEntity\EntityInterface;
use App\DaViEntity\EntityTypeAttributesReader;
use App\DaViEntity\Schema\EntitySchema;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use App\EntityTypes\NullEntity\NullRepository;
use App\Services\AbstractLocator;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

class RepositoryLocator extends AbstractLocator {

  public function __construct(
    private readonly EntityTypeSchemaRegister $entityTypeSchemaRegister,
    #[AutowireLocator('entity_management.entity_repository')]
    ServiceLocator $services
  ) {
    parent::__construct($services);
  }

  public function getEntityRepository(string | EntitySchema $entitySchema, string $version): RepositoryInterface {
    if(is_string($entitySchema)) {
      $entitySchema = $this->entityTypeSchemaRegister->getSchemaFromEntityClass($entitySchema);
    }
    $class = $entitySchema->getRepositoryClass($version);

    if($this->has($class)) {
      return $this->get($class);
    } else {
      return $this->get(NullRepository::class);
    }
  }

}