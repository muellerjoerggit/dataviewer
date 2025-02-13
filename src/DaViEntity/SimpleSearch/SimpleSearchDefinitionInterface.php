<?php

namespace App\DaViEntity\SimpleSearch;

interface SimpleSearchDefinitionInterface {

  public function getSimpleSearchClass(): string;

  public function isValid(): bool;

}