<?php

namespace App\EntityServices\DataProvider;

use App\DaViEntity\Schema\EntitySchema;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use App\Services\AbstractLocator;
use App\Services\ClientService;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

class DataProviderLocator extends AbstractLocator {

  public function __construct(
    private readonly EntityTypeSchemaRegister $entityTypeSchemaRegister,
    private readonly ClientService $clientService,
    #[AutowireLocator('entity_management.entity_data_provider')]
    ServiceLocator $services
  ) {
    parent::__construct($services);
  }

  public function getEntityDataProvider(string | EntitySchema $entitySchema, string $client): DataProviderInterface {
    if(is_string($entitySchema)) {
      $entitySchema = $this->entityTypeSchemaRegister->getSchemaFromEntityClass($entitySchema);
    }
    $version = $this->clientService->getClientVersion($client);
    $class = $entitySchema->getDataProviderClass($version);

    if($this->has($class)) {
      return $this->get($class);
    } else {
      return $this->get(NullDataProvider::class);
    }
  }

}