<?php

namespace App\DaViEntity\Schema\Attribute;

interface EntityOverviewDefinitionInterface {

  public function getLabel(): string;

  public function getPath(): string;

  public function getRank(): int;

}