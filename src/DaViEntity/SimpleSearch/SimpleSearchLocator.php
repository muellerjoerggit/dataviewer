<?php

namespace App\DaViEntity\SimpleSearch;

use App\DaViEntity\Schema\EntitySchema;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use App\Services\AbstractLocator;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

class SimpleSearchLocator extends AbstractLocator {

  public function __construct(
    private readonly EntityTypeSchemaRegister $entityTypeSchemaRegister,
    #[AutowireLocator('entity_management.simple_search')]
    ServiceLocator $services
  ) {
    parent::__construct($services);
  }

  public function getFastSearch(string | EntitySchema $entitySchema, string $version): SimpleSearchInterface {
    if(is_string($entitySchema)) {
      $entitySchema = $this->entityTypeSchemaRegister->getSchemaFromEntityClass($entitySchema);
    }
    $class = $entitySchema->getSimpleSearchClass($version);

    if($this->has($class)) {
      return $this->get($class);
    } else {
      return $this->get(NullSimpleSearch::class);
    }
  }

}