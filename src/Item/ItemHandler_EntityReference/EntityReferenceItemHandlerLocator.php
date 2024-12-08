<?php

namespace App\Item\ItemHandler_EntityReference;

use App\Item\ItemConfigurationInterface;
use App\Item\ItemHandler\ItemHandlerInterface;
use App\Item\ItemInterface;
use App\Services\AbstractLocator;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

class EntityReferenceItemHandlerLocator extends AbstractLocator {

	public function __construct(
    #[AutowireLocator('entity_reference_item_handler')]
    ServiceLocator $services
  ) {
		parent::__construct($services);
	}

	public function getEntityReferenceHandlerFromItem(ItemConfigurationInterface $itemConfiguration): EntityReferenceItemHandlerInterface {
		if($itemConfiguration instanceof ItemInterface) {
			$itemConfiguration = $itemConfiguration->getConfiguration();
		}

		$handler = $itemConfiguration->getHandlerByType(ItemHandlerInterface::HANDLER_ENTITY_REFERENCE);

		if($this->has($handler)) {
			return $this->get($handler);
		} else {
			return $this->get(NullEntityReferenceItemHandler::class);
		}
	}

}
