<?php

namespace App\DaViEntity\Refiner;

use App\DaViEntity\EntityInterface;

class NullRefiner implements RefinerInterface {

  public function refineEntity(EntityInterface $entity): void {}

}