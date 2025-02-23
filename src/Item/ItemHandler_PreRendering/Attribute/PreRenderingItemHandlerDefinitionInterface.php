<?php

namespace App\Item\ItemHandler_PreRendering\Attribute;

use App\Item\ItemHandler\Attribute\ItemHandlerDefinitionInterface;

interface PreRenderingItemHandlerDefinitionInterface extends ItemHandlerDefinitionInterface {

  public function getFormatterOutput(): int;

}