<?php

namespace App\EntityServices\AvailabilityVerdict;

use App\DaViEntity\Schema\EntitySchema;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use App\Services\AbstractLocator;
use App\Services\ClientService;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

class AvailabilityVerdictLocator extends AbstractLocator {

  public function __construct(
    private readonly EntityTypeSchemaRegister $entityTypeSchemaRegister,
    private readonly ClientService $clientService,
    #[AutowireLocator('entity_management.availability_verdict')]
    ServiceLocator $services
  ) {
    parent::__construct($services);
  }

  public function getAvailabilityVerdictService(string | EntitySchema $entitySchema, string $client): AvailabilityVerdictServiceInterface {
    if(is_string($entitySchema)) {
      $entitySchema = $this->entityTypeSchemaRegister->getSchemaFromEntityClass($entitySchema);
    }
    $version = $this->clientService->getClientVersion($client);
    $class = $entitySchema->getAvailabilityVerdictServiceClass($version);

    if($this->has($class)) {
      return $this->get($class);
    } else {
      return $this->get(NullAvailabilityVerdictService::class);
    }
  }

}