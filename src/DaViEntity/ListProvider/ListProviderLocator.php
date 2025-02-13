<?php

namespace App\DaViEntity\ListProvider;

use App\DaViEntity\EntityInterface;
use App\DaViEntity\EntityTypeAttributesReader;
use App\DaViEntity\Schema\EntitySchema;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use App\Services\AbstractLocator;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

class ListProviderLocator extends AbstractLocator {

  public function __construct(
    private readonly EntityTypeSchemaRegister $entityTypeSchemaRegister,
    #[AutowireLocator('entity_management.entity_list_provider')]
    ServiceLocator $services
  ) {
    parent::__construct($services);
  }

  public function getEntityListProvider(string | EntitySchema $entitySchema, string $version): ListProviderInterface {
    if(is_string($entitySchema)) {
      $entitySchema = $this->entityTypeSchemaRegister->getSchemaFromEntityClass($entitySchema);
    }
    $class = $entitySchema->getListProviderClass($version);

    if($this->has($class)) {
      return $this->get($class);
    } else {
      return $this->get(NullListProvider::class);
    }
  }

}