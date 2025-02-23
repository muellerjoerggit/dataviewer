<?php

namespace App\DaViEntity\Validator;

use App\Database\SqlFilter\FilterContainer;
use App\DaViEntity\EntityInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('entity_management.validator')]
interface ValidatorInterface {

  public function validateEntity(EntityInterface $entity): void;

}