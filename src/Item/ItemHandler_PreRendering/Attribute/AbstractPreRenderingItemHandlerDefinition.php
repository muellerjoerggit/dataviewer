<?php

namespace App\Item\ItemHandler_PreRendering\Attribute;

use App\Item\ItemHandler\Attribute\AbstractItemHandlerDefinition;

abstract class AbstractPreRenderingItemHandlerDefinition extends AbstractItemHandlerDefinition implements PreRenderingItemHandlerDefinitionInterface {

  public function __construct(
    string $handlerClass
  ) {
    parent::__construct($handlerClass);
  }

}