<?php

namespace App\DaViEntity\Refiner;

use App\DaViEntity\EntityInterface;

class NullEntityRefiner implements EntityRefinerInterface {

  public function refineEntity(EntityInterface $entity): void {}

}