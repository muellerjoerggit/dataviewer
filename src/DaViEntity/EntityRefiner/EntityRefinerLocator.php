<?php

namespace App\DaViEntity\EntityRefiner;

use App\DaViEntity\EntityInterface;
use App\DaViEntity\EntityTypeAttributesReader;
use App\Services\AbstractLocator;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

class EntityRefinerLocator extends AbstractLocator {

  public function __construct(
    private readonly EntityTypeAttributesReader $entityTypeAttributesReader,
    #[AutowireLocator('entity_management.entity_refiner')]
    ServiceLocator $services
  ) {
    parent::__construct($services);
  }

  public function getEntityRefiner(string | EntityInterface $entityClass): EntityRefinerInterface {
    $class = $this->entityTypeAttributesReader->getEntityRefinerClass($entityClass);

    if($this->has($class)) {
      return $this->get($class);
    } else {
      return $this->get(NullEntityRefiner::class);
    }
  }

}