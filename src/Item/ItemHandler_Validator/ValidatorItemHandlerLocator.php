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
		if($itemConfiguration instanceof ItemInterface) {
			$itemConfiguration = $itemConfiguration->getConfiguration();
		}

		$ret = [];
		$handlers = $itemConfiguration->getHandlerByType(ItemHandlerInterface::HANDLER_VALIDATOR);

		if(!$handlers) {
			return [];
		}

		foreach($handlers as $handler) {
			$ret[] = $this->getHandler($handler);
		}

		return $ret;
	}

	/**
	 * @param array $validationSetting
	 * @return array
	 * @deprecated
	 */
	public function getValidatorHandlerFromEntityReferenceSetting(array $validationSetting): array {
		$ret = [];
		foreach($validationSetting as $handler => $settings) {
			$ret[] = $this->getHandler($handler);
		}

		return $ret;
	}

	public function getHandler(string $handler): ValidatorItemHandlerInterface {
		if($handler && $this->has($handler)) {
			return $this->get($handler);
		}
		return $this->get(NullValidatorItemHandler::class);
	}

}
