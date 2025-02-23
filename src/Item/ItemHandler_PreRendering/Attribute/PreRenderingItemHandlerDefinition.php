<?php

namespace App\Item\ItemHandler_PreRendering\Attribute;

use App\Item\ItemHandler\PreRenderingOptions;
use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class PreRenderingItemHandlerDefinition extends AbstractPreRenderingItemHandlerDefinition {

  public function __construct(
    string $handlerClass,
    int $formatterOutput = PreRenderingOptions::OUTPUT_RAW_FORMATTED,
  ) {
    parent::__construct($handlerClass, $formatterOutput);
  }

}