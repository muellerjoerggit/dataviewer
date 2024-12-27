<?php

namespace App\DaViEntity;

use App\Services\AbstractLocator;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

class EntityCreatorLocator extends AbstractLocator {

  public function __construct(
    #[AutowireLocator('entity_management.entity_creator')]
    ServiceLocator $services,
    private readonly EntityTypeAttributesReader $entityTypeAttributesReader,
  ) {
    parent::__construct($services);
  }

  public function getEntityCreator(string $entityTypeClass): EntityCreatorInterface {
    $entityCreatorClass = $this->entityTypeAttributesReader->getEntityCreatorClass($entityTypeClass);

    if($this->has($entityCreatorClass)) {
      return $this->get($entityCreatorClass);
    } else {
      return $this->get(CommonEntityCreator::class);
    }


  }

}