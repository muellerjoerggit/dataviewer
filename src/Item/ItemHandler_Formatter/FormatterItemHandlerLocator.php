<?php

namespace App\Item\ItemHandler_Formatter;

use App\Item\ItemConfigurationInterface;
use App\Item\ItemHandler\ItemHandlerInterface;
use App\Item\ItemHandler_EntityReference\EntityReferenceItemHandlerInterface;
use App\Item\ItemHandler_EntityReference\NullEntityReferenceItemHandler;
use App\Services\AbstractLocator;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

class FormatterItemHandlerLocator extends AbstractLocator {

  public function __construct(
    #[AutowireLocator('formatter_item_handler')]
    ServiceLocator $services
  ) {
    parent::__construct($services);
  }

  public function getFormatterHandlerFromItem(ItemConfigurationInterface $itemConfiguration): FormatterItemHandlerInterface {
    if(!$itemConfiguration->hasFormatterHandler()) {
      return $this->getNullItemHandler();
    }

    $handler = $itemConfiguration->getFormatterItemHandlerDefinition()->getHandlerClass();
    if ($this->has($handler)) {
      return $this->get($handler);
    } else {
      return $this->getNullItemHandler();
    }
  }

  private function getNullItemHandler(): FormatterItemHandlerInterface {
    return $this->get(NullFormatterItemHandler::class);
  }

}
