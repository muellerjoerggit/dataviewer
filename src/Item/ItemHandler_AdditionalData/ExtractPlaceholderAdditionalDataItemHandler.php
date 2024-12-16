<?php

namespace App\Item\ItemHandler_AdditionalData;

use App\DataCollections\TableData;
use App\DaViEntity\EntityInterface;
use App\Item\ItemInterface;
use App\Utils\ArrayUtils;
use Symfony\Component\DomCrawler\Crawler;

class ExtractPlaceholderAdditionalDataItemHandler implements AdditionalDataItemHandlerInterface {

  private const MODE_HTML = 'html';

  private const MODE_TEXT = 'text';

  public function getValues(EntityInterface $entity, string $property): TableData|array {
    $item = $entity->getPropertyItem($property);
    $itemConfiguration = $item->getConfiguration();
    $handlerSetting = $itemConfiguration->getLazyLoaderSettings();
    $sourceProperties = $handlerSetting['sourceProperty'] ?? NULL;
    $mode = $handlerSetting['mode'] ?? NULL;

    if (!$sourceProperties || !$mode) {
      return [];
    }

    if (!is_array($sourceProperties)) {
      $sourceProperties = [$sourceProperties];
    }

    $ret = [];

    foreach ($sourceProperties as $sourceProperty) {
      $sourceItem = $entity->getPropertyItem($sourceProperty);
      $ret = array_merge($ret, $this->extractPlaceholders($mode, $sourceItem));
    }

    return array_values(array_unique($ret));
  }

  private function extractPlaceholders(string $mode, ItemInterface $item): array {
    $ret = [];
    foreach ($item->getValuesAsOneDimensionalArray() as $value) {
      $matches = [];
      $text = $this->prepareText($mode, $value);
      preg_match_all('/{[[:alpha:]:_]+}/', $text, $matches);
      $matches = ArrayUtils::flattenArrayToScalarValues($matches);
      $ret = array_merge($ret, $matches);
    }

    return $ret;
  }

  private function prepareText(string $mode, string $value): string {
    switch ($mode) {
      case self::MODE_HTML:
        $body = (new Crawler($value))->filterXPath('descendant-or-self::body');
        return $body->text();
      case self::MODE_TEXT:
        return $value;
    }
    return '';
  }

}
