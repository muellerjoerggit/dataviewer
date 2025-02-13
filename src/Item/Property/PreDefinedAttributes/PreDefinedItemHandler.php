<?php

namespace App\Item\Property\PreDefinedAttributes;

use App\Item\ItemHandler_PreRendering\Attribute\PreRenderingItemHandlerDefinitionAttr;
use App\Item\ItemHandler_PreRendering\CommonPreRenderingItemHandler;
use App\Item\ItemHandler_PreRendering\TablePreRenderingItemHandler;

class PreDefinedItemHandler {

  public static function commonPreRenderingHandler(): array {
    return [
      new PreRenderingItemHandlerDefinitionAttr(
        CommonPreRenderingItemHandler::class,
      ),
    ];
  }

  public static function tablePreRenderingHandler(): array {
    return [
      new PreRenderingItemHandlerDefinitionAttr(
        TablePreRenderingItemHandler::class,
      ),
    ];
  }

}