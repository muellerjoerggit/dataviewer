<?php

namespace App\Item\ItemHandler_PreRendering;

use App\DaViEntity\EntityInterface;
use App\DaViEntity\OverviewBuilder\ExtEntityOverviewEnum;
use App\Item\ItemHandler\PreRenderingOptions;
use App\Item\ItemHandler_Formatter\FormatterItemHandlerInterface;
use App\Item\ItemHandler_Formatter\FormatterItemHandlerLocator;
use App\Item\ItemHandler_PreRendering\Attribute\PreRenderingItemHandlerDefinitionInterface;
use App\Item\ItemInterface;

abstract class AbstractPreRenderingItemHandler implements PreRenderingItemHandlerInterface {

  protected FormatterItemHandlerLocator $formatterLocator;

  public function __construct(FormatterItemHandlerLocator $formatterLocator) {
    $this->formatterLocator = $formatterLocator;
  }

  public function getComponentPreRenderArray(EntityInterface $entity, string $property): array {
    $item = $entity->getPropertyItem($property);
    $values = $this->buildValues($item);

    return [
      'component' => 'CommonItem',
      'name' => $item->getConfiguration()->getItemName(),
      'documentation' => [
        'label' => $item->getConfiguration()->getLabel(),
        'description' => $item->getConfiguration()->getDescription(),
        //				'deprecated' => $item->getConfiguration()->getDeprecated(),
      ],
      'data' => [
        'values' => $values,
        'isNull' => $item->isValuesNull(),
        'criticalError' => $item->isRedError(),
        'warningError' => $item->isYellowError(),
      ],
    ];
  }

  protected function buildValues(ItemInterface $item): array {
    $itemConfiguration = $item->getConfiguration();
    $handlerDefinition = $itemConfiguration->getPreRenderingHandlerDefinition();
    $values = $item->getValuesAsArray();

    /** @var $handlerDefinition PreRenderingItemHandlerDefinitionInterface */

    if ($itemConfiguration->hasFormatterHandler()) {
      $handler = $this->formatterLocator->getFormatterHandlerFromItem($item->getConfiguration());

      switch($handlerDefinition->getFormatterOutput()) {
        case PreRenderingOptions::OUTPUT_FORMATTED:
          return $handler->getArrayFormatted($item);
        case PreRenderingOptions::OUTPUT_RAW:
          return $item->getValuesAsArray();
        case PreRenderingOptions::OUTPUT_RAW_FORMATTED:
        default:
          return $handler->getArrayRawFormatted($item);
      }
    }

    return $values;
  }

  public function getExtendedOverview(ItemInterface $item, array $options): array {
    $itemConfiguration = $item->getConfiguration();
    $firstValue = $item->getFirstValueAsString();
    if ($itemConfiguration->hasFormatterHandler()) {
      $handler = $this->formatterLocator->getFormatterHandlerFromItem($itemConfiguration);
      return [
        'type' => ExtEntityOverviewEnum::ADDITIONAL,
        'data' => [
          'text' => $firstValue,
          'additional' => $handler->getValueFormatted($itemConfiguration, $firstValue),
        ],
      ];
    }

    return [
      'type' => ExtEntityOverviewEnum::SCALAR,
      'data' => [
        'text' => $firstValue,
      ],
    ];
  }

}
