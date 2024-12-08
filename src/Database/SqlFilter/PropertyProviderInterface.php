<?php

namespace App\Database\SqlFilter;

interface PropertyProviderInterface {

  public function setProperty(string $property): SqlFilterDefinitionInterface;

  public function getProperty(): string;

}