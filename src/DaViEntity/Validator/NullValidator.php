<?php

namespace App\DaViEntity\Validator;

use App\DaViEntity\EntityInterface;

class NullValidator implements ValidatorInterface {

  public function validateEntity(EntityInterface $entity, array $options = []): void {}

}