<?php

namespace App\DaViEntity\Refiner;

interface RefinerDefinitionInterface {

  public function getRefinerClass(): string;

  public function isValid(): bool;

}