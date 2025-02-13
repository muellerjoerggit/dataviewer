<?php

namespace App\DaViEntity\EntityLabel;

use App\DaViEntity\EntityInterface;
use App\DaViEntity\EntityTypeAttributesReader;
use App\Services\AbstractLocator;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

class LabelCrafterLocator extends AbstractLocator {

  public function __construct(
    private readonly EntityTypeAttributesReader $entityTypeAttributesReader,
    #[AutowireLocator('entity_management.entity_label_crafter')]
    ServiceLocator $services
  ) {
    parent::__construct($services);
  }

  public function getEntityLabelCrafter(string | EntityInterface $entityClass): LabelCrafterInterface {
    $class = $this->entityTypeAttributesReader->getEntityLabelCrafterClass($entityClass);

    if($this->has($class)) {
      return $this->get($class);
    } else {
      return $this->get(NullLabelCrafter::class);
    }
  }

}