<?php

namespace App\EntityServices\Refiner;

use App\DaViEntity\Schema\EntitySchema;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use App\Services\AbstractLocator;
use App\Services\ClientService;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

class RefinerLocator extends AbstractLocator {

  public function __construct(
    private readonly EntityTypeSchemaRegister $entityTypeSchemaRegister,
    private readonly ClientService $clientService,
    #[AutowireLocator('entity_management.entity_refiner')]
    ServiceLocator $services
  ) {
    parent::__construct($services);
  }

  public function getEntityRefiner(string | EntitySchema $entitySchema, string $client): RefinerInterface {
    if(is_string($entitySchema)) {
      $entitySchema = $this->entityTypeSchemaRegister->getSchemaFromEntityClass($entitySchema);
    }
    $version = $this->clientService->getClientVersion($client);
    $class = $entitySchema->getRefinerClass($version);

    if($this->has($class)) {
      return $this->get($class);
    } else {
      return $this->get(NullRefiner::class);
    }
  }

}