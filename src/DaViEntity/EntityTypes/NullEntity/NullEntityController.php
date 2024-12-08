<?php

namespace App\DaViEntity\EntityTypes\NullEntity;

use App\DaViEntity\AbstractEntityController;
use App\DaViEntity\Attribute\EntityType;
use App\DaViEntity\EntityInterface;

#[EntityType(name: 'NullEntity')]
class NullEntityController extends AbstractEntityController {

  public function createNullEntity($client): EntityInterface {
    return new NullEntity($this->schema, $client);
  }

}