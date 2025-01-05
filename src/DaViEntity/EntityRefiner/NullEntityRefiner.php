<?php

namespace App\DaViEntity\EntityRefiner;

use App\DaViEntity\EntityInterface;

class NullEntityRefiner implements EntityRefinerInterface {

  public function refineEntity(EntityInterface $entity): void {}

}