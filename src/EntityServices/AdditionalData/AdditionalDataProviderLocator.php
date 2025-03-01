<?php

namespace App\EntityServices\AdditionalData;

use App\DaViEntity\Schema\EntitySchema;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use App\Services\AbstractLocator;
use App\Services\ClientService;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

class AdditionalDataProviderLocator extends AbstractLocator {

  public function __construct(
    private readonly EntityTypeSchemaRegister $entityTypeSchemaRegister,
    private readonly ClientService $clientService,
    #[AutowireLocator('entity_management.additional_data_provider')]
    ServiceLocator $services
  ) {
    parent::__construct($services);
  }

  /**
   * @return AdditionalDataProviderInterface[]
   */
  public function getAdditionalDataProviders(string | EntitySchema $entitySchema, string $client): array {
    if(is_string($entitySchema)) {
      $entitySchema = $this->entityTypeSchemaRegister->getSchemaFromEntityClass($entitySchema);
    }
    $version = $this->clientService->getClientVersion($client);

    $ret = [];
    foreach ($entitySchema->iterateAdditionalDataProviderClasses($version) as $class) {
      if ($this->has($class)) {
        $ret[] = $this->get($class);
      }
    }
    return $ret;
  }

}