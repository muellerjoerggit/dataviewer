<?php

namespace App\EntityServices\Refiner;

use App\DaViEntity\EntityInterface;

class NullRefiner implements RefinerInterface {

  public function refineEntity(EntityInterface $entity): void {}

  public function setAvailability(EntityInterface $entity): void {}

}