<?php

namespace App\DaViEntity\DataProvider;

interface DataProviderDefinitionInterface {

  public function getDataProviderClass(): string;

  public function isValid(): bool;

}