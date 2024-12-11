<?php

namespace App\Item\ItemHandler_PreRendering;

use App\Item\ItemConfigurationInterface;
use App\Item\ItemHandler\ItemHandlerInterface;
use App\Services\AbstractLocator;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

class PreRenderingItemHandlerLocator extends AbstractLocator {

  public function __construct(
    #[AutowireLocator('pre_rendering_item_handler')]
    ServiceLocator $services
  ) {
    parent::__construct($services);
  }

  public function getPreRenderingHandlerFromItem(ItemConfigurationInterface $itemConfiguration): PreRenderingItemHandlerInterface {
    $handlerName = $itemConfiguration->getHandlerByType(ItemHandlerInterface::HANDLER_PRE_RENDERING);
    if ($handlerName && $this->has($handlerName)) {
      return $this->get($handlerName);
    } else {
      return $this->get(NullPreRenderingItemHandler::class);
    }
  }

}
