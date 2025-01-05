<?php

namespace App\DaViEntity\EntityColumnBuilder;

use App\DaViEntity\EntityInterface;
use App\DaViEntity\EntityTypeAttributesReader;
use App\Services\AbstractLocator;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

class EntityColumnBuilderLocator extends AbstractLocator {

  public function __construct(
    private readonly EntityTypeAttributesReader $entityTypeAttributesReader,
    #[AutowireLocator('entity_management.entity_column_builder')]
    ServiceLocator $services
  ) {
    parent::__construct($services);
  }

  public function getEntityColumnBuilder(string | EntityInterface $entityClass): EntityColumnBuilderInterface {
    $class = $this->entityTypeAttributesReader->getEntityColumnBuilderClass($entityClass);

    if($this->has($class)) {
      return $this->get($class);
    } else {
      return $this->get(NullEntityColumnBuilder::class);
    }
  }

}