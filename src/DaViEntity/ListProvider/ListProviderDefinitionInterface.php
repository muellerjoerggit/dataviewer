<?php

namespace App\DaViEntity\ListProvider;

interface ListProviderDefinitionInterface {

  public function getListProviderClass(): string;

  public function isValid(): bool;

}