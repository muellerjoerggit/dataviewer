<?php

namespace App\Item\ItemHandler_PreRendering;

use App\DaViEntity\EntityViewBuilderInterface;
use App\Item\ItemHandler_ValueFormatter\ValueFormatterItemHandlerLocator;
use App\DaViEntity\EntityInterface;
use App\Item\ItemInterface;

class JsonPreRenderingItemHandler extends AbstractPreRenderingItemHandler {

	public function __construct(ValueFormatterItemHandlerLocator $formatterLocator) {
		parent::__construct($formatterLocator);
	}

	public function getComponentPreRenderArray(EntityInterface $entity, string $property): array {
		$item = $entity->getPropertyItem($property);

		return [
			'component' => 'JsonItem',
			'name' => $item->getConfiguration()->getName(),
			'documentation' => [
				'label' => $item->getConfiguration()->getLabel(),
				'description' => $item->getConfiguration()->getDescription(),
//				'deprecated' => $item->getConfiguration()->getDeprecated(),
			],
			'data' => [
				'values' => $item->getValuesAsArray(),
				'isNull' => $item->isValuesNull(),
				'criticalError' => $item->isRedError(),
				'warningError' => $item->isYellowError()
			]
		];
	}

  public function getExtendedOverview(ItemInterface $item, array $options): array {
    return [
      'type' => EntityViewBuilderInterface::EXT_OVERVIEW_JSON,
      'data' => [
        'json' => $item->getFirstValueAsString()
      ]];
  }

}
