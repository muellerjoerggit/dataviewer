<?php

namespace App\DaViEntity\Validator;

use App\DaViEntity\EntityInterface;
use App\DaViEntity\EntityTypeAttributesReader;
use App\Services\AbstractLocator;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

class ValidatorLocator extends AbstractLocator {

  public function __construct(
    private readonly EntityTypeAttributesReader $entityTypeAttributesReader,
    #[AutowireLocator('entity_management.validator')]
    ServiceLocator $services
  ) {
    parent::__construct($services);
  }

  public function getValidator(string | EntityInterface $entityClass): ValidatorInterface {
    $class = $this->entityTypeAttributesReader->getEntityDataProviderClass($entityClass);

    if($this->has($class)) {
      return $this->get($class);
    } else {
      return $this->get(NullValidator::class);
    }
  }

}