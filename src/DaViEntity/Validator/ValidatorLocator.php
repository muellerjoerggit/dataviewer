<?php

namespace App\DaViEntity\Validator;

use App\DaViEntity\Schema\EntitySchema;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use App\Services\AbstractLocator;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

class ValidatorLocator extends AbstractLocator {

  public function __construct(
    private readonly EntityTypeSchemaRegister $entityTypeSchemaRegister,
    #[AutowireLocator('entity_management.validator')]
    ServiceLocator $services
  ) {
    parent::__construct($services);
  }

  public function getValidator(string | EntitySchema $entitySchema, string $version): ValidatorInterface {
    if(is_string($entitySchema)) {
      $entitySchema = $this->entityTypeSchemaRegister->getSchemaFromEntityClass($entitySchema);
    }
    $class = $entitySchema->getValidatorClass($version);

    if($this->has($class)) {
      return $this->get($class);
    } else {
      return $this->get(NullValidator::class);
    }
  }

}