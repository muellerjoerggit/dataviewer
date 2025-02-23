<?php

namespace App\Item\ItemHandler_AdditionalData;

use App\Item\ItemConfigurationInterface;
use App\Item\ItemHandler\ItemHandlerInterface;
use App\Services\AbstractLocator;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

class AdditionalDataItemHandlerLocator extends AbstractLocator {

  public function __construct(
    #[AutowireLocator('additional_data_item_handler')]
    ServiceLocator $services
  ) {
    parent::__construct($services);
  }

  public function getAdditionalDataItemHandler(ItemConfigurationInterface $itemConfiguration): AdditionalDataItemHandlerInterface {
    $handler = $itemConfiguration->getAdditionalDataHandlerDefinition()->getHandlerClass();
    if ($handler && $this->has($handler)) {
      return $this->get($handler);
    } else {
      return $this->get(NullAdditionalDataItemHandler::class);
    }
  }

}
