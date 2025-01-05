<?php

namespace App\DaViEntity\EntityDataProvider;

use App\DaViEntity\EntityInterface;
use App\DaViEntity\EntityTypeAttributesReader;
use App\Services\AbstractLocator;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

class EntityDataProviderLocator extends AbstractLocator {

  public function __construct(
    private readonly EntityTypeAttributesReader $entityTypeAttributesReader,
    #[AutowireLocator('entity_management.entity_data_provider')]
    ServiceLocator $services
  ) {
    parent::__construct($services);
  }

  public function getEntityDataProvider(string | EntityInterface $entityClass): EntityDataProviderInterface {
    $class = $this->entityTypeAttributesReader->getEntityDataProviderClass($entityClass);

    if($this->has($class)) {
      return $this->get($class);
    } else {
      return $this->get(NullEntityDataProvider::class);
    }
  }

}