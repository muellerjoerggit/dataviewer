<?php

namespace App\DaViEntity\ColumnBuilder;

use App\DaViEntity\EntityInterface;
use App\DaViEntity\EntityTypeAttributesReader;
use App\DaViEntity\Schema\EntitySchema;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use App\Services\AbstractLocator;
use App\Services\ClientService;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

class ColumnBuilderLocator extends AbstractLocator {

  public function __construct(
    private readonly EntityTypeSchemaRegister $entityTypeSchemaRegister,
    private readonly ClientService $clientService,
    #[AutowireLocator('entity_management.entity_column_builder')]
    ServiceLocator $services
  ) {
    parent::__construct($services);
  }

  public function getEntityColumnBuilder(string | EntitySchema $entitySchema, string $client): ColumnBuilderInterface {
    if(is_string($entitySchema)) {
      $entitySchema = $this->entityTypeSchemaRegister->getSchemaFromEntityClass($entitySchema);
    }

    $version = $this->clientService->getClientVersion($client);
    $class = $entitySchema->getColumnsBuilderClass($version);

    if($this->has($class)) {
      return $this->get($class);
    } else {
      return $this->get(NullColumnBuilder::class);
    }
  }

}