<?php

namespace App\EntityServices\AggregatedData;

use App\DaViEntity\ListProvider\ListProviderInterface;
use App\DaViEntity\ListProvider\NullListProvider;
use App\DaViEntity\Schema\EntitySchema;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use App\Services\AbstractLocator;
use App\Services\ClientService;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

class AggregatedDataProviderLocator extends AbstractLocator {

  public function __construct(
    private readonly EntityTypeSchemaRegister $entityTypeSchemaRegister,
    private readonly ClientService $clientService,
    #[AutowireLocator('entity_management.aggregated_data_provider')]
    ServiceLocator $services
  ) {
    parent::__construct($services);
  }

  public function getAggregatedDataProvider(string | EntitySchema $entitySchema, string $client): AggregatedDataProviderInterface {
    if(is_string($entitySchema)) {
      $entitySchema = $this->entityTypeSchemaRegister->getSchemaFromEntityClass($entitySchema);
    }
    $version = $this->clientService->getClientVersion($client);
    $class = $entitySchema->getAggregatedDataProviderClass($version);

    if($this->has($class)) {
      return $this->get($class);
    } else {
      return $this->get(NullAggregatedDataProvider::class);
    }
  }

}