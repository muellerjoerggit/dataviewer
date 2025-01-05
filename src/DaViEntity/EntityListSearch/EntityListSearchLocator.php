<?php

namespace App\DaViEntity\EntityListSearch;

use App\DaViEntity\EntityInterface;
use App\DaViEntity\EntityTypeAttributesReader;
use App\Services\AbstractLocator;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

class EntityListSearchLocator extends AbstractLocator {

  public function __construct(
    private readonly EntityTypeAttributesReader $entityTypeAttributesReader,
    #[AutowireLocator('entity_management.entity_list_search')]
    ServiceLocator $services
  ) {
    parent::__construct($services);
  }

  public function getEntityListSearchClass(string | EntityInterface $entityClass): EntityListSearchInterface {
    $class = $this->entityTypeAttributesReader->getEntityListSearchClass($entityClass);

    if($this->has($class)) {
      return $this->get($class);
    } else {
      return $this->get(NullEntityListSearch::class);
    }
  }

}