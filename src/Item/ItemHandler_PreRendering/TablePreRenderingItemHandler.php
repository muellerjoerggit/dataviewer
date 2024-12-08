<?php

namespace App\Item\ItemHandler_PreRendering;

use App\DataCollections\TableData;
use App\Item\ItemHandler_ValueFormatter\ValueFormatterItemHandlerLocator;
use App\DaViEntity\EntityInterface;

class TablePreRenderingItemHandler extends AbstractPreRenderingItemHandler {

	public function __construct(ValueFormatterItemHandlerLocator $formatterLocator) {
		parent::__construct($formatterLocator);
	}

	public function getComponentPreRenderArray(EntityInterface $entity, string $property): array {
		$item = $entity->getPropertyItem($property);
		$aggregatedData = $item->getRawValues();

		$preRender = [
			'component' => 'TableItem',
			'name' => $item->getConfiguration()->getName(),
			'documentation' => [
				'label' => $item->getConfiguration()->getLabel(),
				'description' => $item->getConfiguration()->getDescription(),
//				'deprecated' => $item->getConfiguration()->getDeprecated(),
			],
			'data' => [
				'header' => [],
				'tableRows' => [],
				'isNull' => true,
				'criticalError' => $item->isRedError(),
				'warningError' => $item->isYellowError()
			]
		];

		if($aggregatedData instanceof TableData) {
			$preRender['data']['header'] = $aggregatedData->getHeader();
			$preRender['data']['tableRows'] = $aggregatedData->getRows();
			$preRender['data']['isNull'] = false;
		}

		return $preRender;
	}

}
