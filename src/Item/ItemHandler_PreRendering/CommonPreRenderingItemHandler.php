<?php

namespace App\Item\ItemHandler_PreRendering;

use App\Item\ItemHandler_ValueFormatter\ValueFormatterItemHandlerLocator;

class CommonPreRenderingItemHandler extends AbstractPreRenderingItemHandler {

  public function __construct(ValueFormatterItemHandlerLocator $formatterLocator) {
    parent::__construct($formatterLocator);
  }

}
