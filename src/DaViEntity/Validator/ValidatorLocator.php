<?php

namespace App\DaViEntity\Validator;

use App\DaViEntity\Schema\EntitySchema;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use App\Services\AbstractLocator;
use App\Services\ClientService;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

class ValidatorLocator extends AbstractLocator {

  public function __construct(
    private readonly EntityTypeSchemaRegister $entityTypeSchemaRegister,
    private readonly ClientService $clientService,
    #[AutowireLocator('entity_management.validator')]
    ServiceLocator $services
  ) {
    parent::__construct($services);
  }

  public function getValidator(string | EntitySchema $entitySchema, string $client): ValidatorInterface {
    if(is_string($entitySchema)) {
      $entitySchema = $this->entityTypeSchemaRegister->getSchemaFromEntityClass($entitySchema);
    }
    $version = $this->clientService->getClientVersion($client);
    $class = $entitySchema->getValidatorClass($version);

    if($this->has($class)) {
      return $this->get($class);
    } else {
      return $this->get(NullValidator::class);
    }
  }

}