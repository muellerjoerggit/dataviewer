<?php

namespace App\Item\ItemHandler_AdditionalData;

use App\Item\ItemConfigurationInterface;
use App\Item\ItemHandler\ItemHandlerInterface;
use App\Item\ItemInterface;
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
		$handlerName = $itemConfiguration->getHandlerByType(ItemHandlerInterface::HANDLER_ADDITIONAL_DATA);
		if($handlerName && $this->has($handlerName)) {
			return $this->get($handlerName);
		} else {
			return $this->get(NullAdditionalDataItemHandler::class);
		}
	}

}
