<?php

namespace App\Item\ItemHandler_PreRendering;

use App\DaViEntity\EntityInterface;
use App\Item\ItemHandler_Formatter\FormatterItemHandlerLocator;

class ColorPreRenderingItemHandler extends AbstractPreRenderingItemHandler {

  public function __construct(FormatterItemHandlerLocator $formatterLocator) {
    parent::__construct($formatterLocator);
  }

  public function getComponentPreRenderArray(EntityInterface $entity, string $property): array {
    $item = $entity->getPropertyItem($property);

    return [
      'component' => 'ColorItem',
      'name' => $item->getConfiguration()->getItemName(),
      'documentation' => [
        'label' => $item->getConfiguration()->getLabel(),
        'description' => $item->getConfiguration()->getDescription(),
        //				'deprecated' => $item->getConfiguration()->getDeprecated(),
      ],
      'data' => [
        'values' => $item->getValuesAsArray(),
        'isNull' => $item->isValuesNull(),
        'criticalError' => $item->isRedError(),
        'warningError' => $item->isYellowError(),
      ],
    ];
  }

}
