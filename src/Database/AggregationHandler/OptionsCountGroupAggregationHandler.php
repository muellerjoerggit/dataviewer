<?php

namespace App\Database\AggregationHandler;

use App\Database\Aggregation\AggregationConfiguration;
use App\Database\Aggregation\AggregationHandlerInterface;
use App\Database\DaViQueryBuilder;
use App\DataCollections\TableData;
use App\DaViEntity\Schema\EntitySchema;
use App\Item\ItemConfigurationInterface;
use App\Item\ItemHandler_Formatter\FormatterItemHandlerInterface;
use App\Item\ItemHandler_Formatter\FormatterItemHandlerLocator;

class OptionsCountGroupAggregationHandler extends AbstractAggregationHandler {

  private FormatterItemHandlerLocator $formatterHandlerLocator;

  public function __construct(FormatterItemHandlerLocator $formatterHandlerLocator) {
    $this->formatterHandlerLocator = $formatterHandlerLocator;
  }

  public function buildAggregatedQueryBuilder(EntitySchema $schema, DaViQueryBuilder $queryBuilder, AggregationConfiguration $aggregationConfiguration, array $options = []): void {
    $properties = $aggregationConfiguration->getProperties();
    $blackList = $options[AggregationHandlerInterface::YAML_PARAM_PROPERTY_BLACKLIST] ?? [];

    $queryBuilder->select('COUNT(*) AS ' . AggregationHandlerInterface::YAML_PARAM_COUNT_COLUMN);
    foreach ($properties as $property => $expressionName) {
      if (!is_string($property) || array_key_exists($property, $blackList)) {
        continue;
      }

      $queryBuilder->addSelect($property);
      $queryBuilder->addGroupBy($property);
    }
  }

  public function processingAggregatedData(DaViQueryBuilder $queryBuilder, EntitySchema $schema, AggregationConfiguration $aggregationConfiguration): mixed {
    $data = $this->executeQueryBuilder($queryBuilder);

    $headerColumns = $aggregationConfiguration->getSetting('header');
    $aggregatedColumns = $aggregationConfiguration->getProperties();
    $items = [];
    foreach (array_keys($aggregatedColumns) as $columnName) {
      if (!$schema->hasProperty($columnName)) {
        continue;
      }
      $items[$columnName] = $schema->getProperty($columnName);
    }

    $header = [];
    $tableRows = [];

    foreach ($data as $index => $row) {
      foreach ($row as $columnName => $value) {
        $handler = NULL;
        if (!isset($header[$columnName])) {
          $header[$columnName] = $headerColumns[$columnName] ?? $columnName;
        }

        $item = $items[$columnName] ?? FALSE;
        if ($item instanceof ItemConfigurationInterface && $item->hasFormatterHandler()) {
          $handler = $this->formatterHandlerLocator->getFormatterHandlerFromItem($item);
        }

        if ($handler instanceof FormatterItemHandlerInterface) {
          $tableRows[$index][$columnName] = $handler->getValueRawFormatted($item, $value);
        } else {
          $tableRows[$index][$columnName] = $value;
        }
      }
    }

    return new TableData($header, $tableRows);
  }

}
