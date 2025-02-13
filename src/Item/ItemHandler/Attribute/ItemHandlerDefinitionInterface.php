<?php

namespace App\Item\ItemHandler\Attribute;

interface ItemHandlerDefinitionInterface {

  public function getHandlerClass(): string;

  public function isValid(): bool;

}