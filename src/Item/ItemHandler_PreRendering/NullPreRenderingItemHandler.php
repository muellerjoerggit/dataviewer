<?php

namespace App\Item\ItemHandler_PreRendering;

use App\Item\ItemHandler_Formatter\FormatterItemHandlerLocator;

class NullPreRenderingItemHandler extends AbstractPreRenderingItemHandler {

  public function __construct(FormatterItemHandlerLocator $formatterLocator) {
    parent::__construct($formatterLocator);
  }

}
