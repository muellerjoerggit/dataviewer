<?php

namespace App\DaViEntity\Schema\Attribute;

interface LabelDefinitionInterface {

  public function getLabel(): string;

  public function getPath(): string;

  public function getRank(): int;

  public function setProperty(string $property): static;

}