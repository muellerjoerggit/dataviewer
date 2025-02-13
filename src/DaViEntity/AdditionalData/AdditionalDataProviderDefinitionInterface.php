<?php

namespace App\DaViEntity\AdditionalData;

interface AdditionalDataProviderDefinitionInterface {

  public function getAdditionalDataProviderClass(): string;

}