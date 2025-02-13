<?php

namespace App\DaViEntity\ListProvider;

use App\DaViEntity\EntityInterface;
use App\DaViEntity\EntityTypeAttributesReader;
use App\Services\AbstractLocator;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

class ListProviderLocator extends AbstractLocator {

  public function __construct(
    private readonly EntityTypeAttributesReader $entityTypeAttributesReader,
    #[AutowireLocator('entity_management.entity_list_provider')]
    ServiceLocator $services
  ) {
    parent::__construct($services);
  }

  public function getEntityListProvider(string | EntityInterface $entityClass): ListProviderInterface {
    $class = $this->entityTypeAttributesReader->getEntityListProviderClass($entityClass);

    if($this->has($class)) {
      return $this->get($class);
    } else {
      return $this->get(NullListProvider::class);
    }
  }

}