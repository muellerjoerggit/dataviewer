<?php

namespace App\EntityServices\EntityLabel;

use App\DaViEntity\EntityInterface;
use App\DaViEntity\EntityTypeAttributesReader;
use App\DaViEntity\Schema\EntitySchema;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use App\Services\AbstractLocator;
use App\Services\ClientService;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

class LabelCrafterLocator extends AbstractLocator {

  public function __construct(
    private readonly EntityTypeSchemaRegister $entityTypeSchemaRegister,
    private readonly ClientService $clientService,
    #[AutowireLocator('entity_management.label_crafter')]
    ServiceLocator $services
  ) {
    parent::__construct($services);
  }

  public function getEntityLabelCrafter(string | EntitySchema $entitySchema, string $client): LabelCrafterInterface {
    if(is_string($entitySchema)) {
      $entitySchema = $this->entityTypeSchemaRegister->getSchemaFromEntityClass($entitySchema);
    }
    $version = $this->clientService->getClientVersion($client);
    $class = $entitySchema->getLabelCrafterClass($version);

    if($this->has($class)) {
      return $this->get($class);
    } else {
      return $this->get(NullLabelCrafter::class);
    }
  }

}