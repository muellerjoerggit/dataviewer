<?php

namespace App\DaViEntity;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('entity_management.entity_controller')]
interface EntityControllerInterface {

  public function getEntityLabel(EntityInterface $entity): string;

}