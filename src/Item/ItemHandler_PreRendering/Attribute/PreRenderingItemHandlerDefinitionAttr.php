<?php

namespace App\Item\ItemHandler_PreRendering\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class PreRenderingItemHandlerDefinitionAttr extends AbstractPreRenderingItemHandlerDefinition {

  public function __construct(
    string $handlerClass
  ) {
    parent::__construct($handlerClass);
  }

}