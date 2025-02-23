<?php

namespace App\DaViEntity\OverviewBuilder;

use App\DaViEntity\Schema\EntitySchema;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use App\Services\AbstractLocator;
use App\Services\ClientService;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

class OverviewBuilderLocator extends AbstractLocator {

  public function __construct(
    private readonly EntityTypeSchemaRegister $entityTypeSchemaRegister,
    private readonly ClientService $clientService,
    #[AutowireLocator('entity_management.overview_builder')]
    ServiceLocator $services
  ) {
    parent::__construct($services);
  }

  public function getOverviewBuilder(string | EntitySchema $entitySchema, string $client): OverviewBuilderInterface {
    if(is_string($entitySchema)) {
      $entitySchema = $this->entityTypeSchemaRegister->getSchemaFromEntityClass($entitySchema);
    }
    $version = $this->clientService->getClientVersion($client);
    $class = $entitySchema->getOverviewBuilderClass($version);

    if($this->has($class)) {
      return $this->get($class);
    } else {
      return $this->get(NullOverviewBuilder::class);
    }
  }

}