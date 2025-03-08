<?php

namespace App\EntityServices\AvailabilityVerdict;

use App\DaViEntity\EntityInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('entity_management.availability_verdict')]
interface AvailabilityVerdictServiceInterface {

  public function setAvailability(EntityInterface $entity): void;

}