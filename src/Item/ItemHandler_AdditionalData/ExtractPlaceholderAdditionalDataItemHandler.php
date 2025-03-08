<?php

namespace App\Item\ItemHandler_AdditionalData;

use App\DataCollections\TableData;
use App\DaViEntity\EntityInterface;
use App\Item\ItemHandler_AdditionalData\Attribute\ExtractPlaceholderAdditionalDataHandlerDefinition;
use App\Item\ItemInterface;
use App\Utils\ArrayUtils;
use Symfony\Component\DomCrawler\Crawler;

class ExtractPlaceholderAdditionalDataItemHandler implements AdditionalDataItemHandlerInterface {

  public function getValues(EntityInterface $entity, string $property): TableData | array | int  {
    $item = $entity->getPropertyItem($property);
    $itemConfiguration = $item->getConfiguration();
    $handlerDefinition = $itemConfiguration->getAdditionalDataHandlerDefinition();

    if(!$handlerDefinition instanceof ExtractPlaceholderAdditionalDataHandlerDefinition || !$handlerDefinition->isValid()) {
      return [];
    }

    $ret = [];

    foreach ($handlerDefinition->getSourceProperties() as $sourceProperty) {
      $sourceItem = $entity->getPropertyItem($sourceProperty);
      $ret = array_merge($ret, $this->extractPlaceholders($handlerDefinition, $sourceItem));
    }

    return array_values(array_unique($ret));
  }

  private function extractPlaceholders(ExtractPlaceholderAdditionalDataHandlerDefinition $definition, ItemInterface $item): array {
    $ret = [];
    foreach ($item->getValuesAsOneDimensionalArray() as $value) {
      $matches = [];
      $text = $this->prepareText($definition, $value);
      preg_match_all('/{[[:alpha:]:_]+}/', $text, $matches);
      $matches = ArrayUtils::flattenArrayToScalarValues($matches);
      $ret = array_merge($ret, $matches);
    }

    return $ret;
  }

  private function prepareText(ExtractPlaceholderAdditionalDataHandlerDefinition $definition, string $value): string {
    switch ($definition->getMode()) {
      case ExtractPlaceholderAdditionalDataHandlerDefinition::MODE_HTML:
        $body = (new Crawler($value))->filterXPath('descendant-or-self::body');
        return $body->text();
      case ExtractPlaceholderAdditionalDataHandlerDefinition::MODE_TEXT:
        return $value;
    }
    return '';
  }

}
