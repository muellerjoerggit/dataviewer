<?php

namespace App\Item\ItemHandler_ValueFormatter;

use App\Item\ItemConfigurationInterface;
use App\Item\ItemHandler\ItemHandlerInterface;
use App\Services\AbstractLocator;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

class ValueFormatterItemHandlerLocator extends AbstractLocator {

  public function __construct(
    #[AutowireLocator('formatter_item_handler')]
    ServiceLocator $services
  ) {
    parent::__construct($services);
  }

  public function getFormatterHandlerFromItem(ItemConfigurationInterface $itemConfiguration): ValueFormatterItemHandlerInterface {
    $handlerName = $itemConfiguration->getHandlerByType(ItemHandlerInterface::HANDLER_VALUE_FORMATTER);
    if ($handlerName && $this->has($handlerName)) {
      return $this->get($handlerName);
    } else {
      return $this->get(NullValueFormatterItemHandler::class);
    }
  }

}
