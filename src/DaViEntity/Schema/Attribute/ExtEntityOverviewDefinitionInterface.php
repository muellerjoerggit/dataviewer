<?php

namespace App\DaViEntity\Schema\Attribute;

interface ExtEntityOverviewDefinitionInterface {

  public function getLabel(): string;

  public function getPath(): string;

  public function getRank(): int;

}