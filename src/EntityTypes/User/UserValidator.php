<?php

namespace App\EntityTypes\User;

use App\DaViEntity\EntityInterface;
use App\DaViEntity\Validator\AbstractValidator;

class UserValidator extends AbstractValidator {

  public function validateEntity(EntityInterface $entity): void {
    parent::validateEntity($entity);
    $roles = $entity->getPropertyItem('roles')->getValuesAsArray();
    if(!in_array(2, $roles)) {
      $this->logItemAndEntity($entity, 'USR-1000', 'roles');
    }
  }

}