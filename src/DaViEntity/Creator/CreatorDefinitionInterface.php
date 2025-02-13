<?php

namespace App\DaViEntity\Creator;

interface CreatorDefinitionInterface {

  public function getCreatorClass(): string;

  public function isValid(): bool;

}