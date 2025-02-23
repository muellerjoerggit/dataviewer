<?php

namespace App\Item\Property\PreDefinedAttributes;

use App\Item\ItemHandler_PreRendering\Attribute\PreRenderingItemHandlerDefinition;
use App\Item\ItemHandler_PreRendering\CommonPreRenderingItemHandler;
use App\Item\ItemHandler_PreRendering\TablePreRenderingItemHandler;

class PreDefinedPreRenderingItemHandler {

  public static function commonPreRenderingHandler(): array {
    return [
      new PreRenderingItemHandlerDefinition(
        CommonPreRenderingItemHandler::class,
      ),
    ];
  }

  public static function tablePreRenderingHandler(): array {
    return [
      new PreRenderingItemHandlerDefinition(
        TablePreRenderingItemHandler::class,
      ),
    ];
  }

}