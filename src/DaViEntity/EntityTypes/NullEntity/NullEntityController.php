<?php

namespace App\DaViEntity\EntityTypes\NullEntity;

use App\DaViEntity\AbstractEntityController;
use App\DaViEntity\EntityInterface;
use App\DaViEntity\Schema\Attribute\EntityTypeAttr;

#[EntityTypeAttr(name: 'NullEntity')]
class NullEntityController extends AbstractEntityController {

  public function createNullEntity($client): EntityInterface {
    return new NullEntity($this->schema, $client);
  }

}