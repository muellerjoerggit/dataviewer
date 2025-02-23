<?php

namespace App\Item\ItemHandler_PreRendering\Attribute;

use App\Item\ItemHandler\Attribute\AbstractItemHandlerDefinition;
use App\Item\ItemHandler\PreRenderingOptions;

abstract class AbstractPreRenderingItemHandlerDefinition extends AbstractItemHandlerDefinition implements PreRenderingItemHandlerDefinitionInterface {

  public function __construct(
    string $handlerClass,
    public readonly int $formatterOutput = PreRenderingOptions::OUTPUT_RAW_FORMATTED,
  ) {
    parent::__construct($handlerClass);
  }

  public function getFormatterOutput(): int {
    return $this->formatterOutput;
  }

}