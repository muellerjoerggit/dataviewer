<?php

namespace App\Item\ItemHandler_PreRendering;

use App\DaViEntity\EntityInterface;
use App\DaViEntity\EntityViewBuilderInterface;
use App\Item\ItemHandler_Formatter\FormatterItemHandlerInterface;
use App\Item\ItemHandler_Formatter\FormatterItemHandlerLocator;
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
    $handlerSettings = $itemConfiguration->getPreRenderingHandlerDefinition();
    $values = $item->getValuesAsArray();

    if ($itemConfiguration->hasFormatterHandler()) {
      $handler = $this->formatterLocator->getFormatterHandlerFromItem($item->getConfiguration());
      $values = $handler->getArrayRawFormatted($item);

      if (!isset($handlerSettings['default_formatter_output'])) {
        return $values;
      } elseif ($handlerSettings['default_formatter_output'] === FormatterItemHandlerInterface::OUTPUT_FORMATTED) {
        $values = $handler->getArrayFormatted($item);
      } elseif ($handlerSettings['default_formatter_output'] === FormatterItemHandlerInterface::OUTPUT_RAW) {
        $values = $item->getValuesAsOneDimensionalArray();
      } elseif ($handlerSettings['default_formatter_output'] === FormatterItemHandlerInterface::OUTPUT_RAW_FORMATTED) {
        $values = $handler->getArrayRawFormatted($item);
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
        'type' => EntityViewBuilderInterface::EXT_OVERVIEW_ADDITIONAL,
        'data' => [
          'text' => $firstValue,
          'additional' => $handler->getValueFormatted($itemConfiguration, $firstValue),
        ],
      ];
    }

    return [
      'type' => EntityViewBuilderInterface::EXT_OVERVIEW_SCALAR,
      'data' => [
        'text' => $firstValue,
      ],
    ];
  }

}
