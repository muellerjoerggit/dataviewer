<?php

namespace App\Item\ItemHandler_Validator;

use App\Item\ItemConfigurationInterface;
use App\Item\ItemHandler\ItemHandlerInterface;
use App\Item\ItemInterface;
use App\Services\AbstractLocator;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

class ValidatorItemHandlerLocator extends AbstractLocator {

  public function __construct(
    #[AutowireLocator('validator_item_handler')]
    ServiceLocator $services
  ) {
    parent::__construct($services);
  }

  public function getValidatorHandlerFromItem(ItemConfigurationInterface $itemConfiguration): array {
    $ret = [];

    if (!$itemConfiguration->hasValidatorHandlerDefinition()) {
      return [];
    }

    foreach ($itemConfiguration->iterateValidatorItemHandlerDefinitions() as $definition) {
      $handler = $definition->getHandlerClass();
      if(isset($ret[$handler])) {
        continue;
      }

      $ret[$handler] = $this->getHandler($handler);
    }

    return $ret;
  }

  public function getHandler(string $handler): ValidatorItemHandlerInterface {
    if ($this->has($handler)) {
      return $this->get($handler);
    }
    return $this->get(NullValidatorItemHandler::class);
  }

}
