<?php

namespace App\DaViEntity\EntityRefiner;

use App\DaViEntity\EntityInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('entity_management.entity_refiner')]
interface EntityRefinerInterface {

  public function refineEntity(EntityInterface $entity): void;

}
