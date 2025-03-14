<?php

namespace App\EntityServices\Refiner;

use App\DaViEntity\EntityInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('entity_management.entity_refiner')]
interface RefinerInterface {

  public function refineEntity(EntityInterface $entity): void;

  public function setAvailability(EntityInterface $entity): void;

}
