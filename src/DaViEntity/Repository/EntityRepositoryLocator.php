<?php

namespace App\DaViEntity\Repository;

use App\DaViEntity\EntityInterface;
use App\DaViEntity\EntityTypeAttributesReader;
use App\EntityTypes\NullEntity\NullRepository;
use App\Services\AbstractLocator;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

class EntityRepositoryLocator extends AbstractLocator {

  public function __construct(
    private readonly EntityTypeAttributesReader $entityTypeAttributesReader,
    #[AutowireLocator('entity_management.entity_repository')]
    ServiceLocator $services
  ) {
    parent::__construct($services);
  }

  public function getEntityRepository(string | EntityInterface $entityClass): EntityRepositoryInterface {
    $class = $this->entityTypeAttributesReader->getRepositoryClass($entityClass);

    if($this->has($class)) {
      return $this->get($class);
    } else {
      return $this->get(NullRepository::class);
    }
  }

}