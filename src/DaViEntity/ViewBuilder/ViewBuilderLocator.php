<?php

namespace App\DaViEntity\ViewBuilder;

use App\DaViEntity\Schema\EntitySchema;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use App\Services\AbstractLocator;
use App\Services\ClientService;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

class ViewBuilderLocator extends AbstractLocator {

  public function __construct(
    private readonly EntityTypeSchemaRegister $entityTypeSchemaRegister,
    private readonly ClientService $clientService,
    #[AutowireLocator('entity_management.view_builder')]
    ServiceLocator $services
  ) {
    parent::__construct($services);
  }

  public function getViewBuilder(string | EntitySchema $entitySchema, string $client): ViewBuilderInterface {
    if(is_string($entitySchema)) {
      $entitySchema = $this->entityTypeSchemaRegister->getSchemaFromEntityClass($entitySchema);
    }
    $version = $this->clientService->getClientVersion($client);
    $class = $entitySchema->getViewBuilderClass($version);

    if($this->has($class)) {
      return $this->get($class);
    } else {
      return $this->get(NullViewBuilder::class);
    }
  }

}