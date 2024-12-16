<?php

namespace App\Database\AggregationHandler;

use App\Database\Aggregation\AggregationConfiguration;
use App\Database\DaViQueryBuilder;
use App\DataCollections\TableData;
use App\DaViEntity\Schema\EntitySchema;
use App\Item\ItemConfigurationInterface;
use App\Item\ItemHandler_ValueFormatter\ValueFormatterItemHandlerInterface;
use App\Item\ItemHandler_ValueFormatter\ValueFormatterItemHandlerLocator;

class OptionsCountGroupAggregationHandler extends AbstractAggregationHandler {

  private ValueFormatterItemHandlerLocator $formatterHandlerLocator;

  public function __construct(ValueFormatterItemHandlerLocator $formatterHandlerLocator) {
    $this->formatterHandlerLocator = $formatterHandlerLocator;
  }

  public function buildAggregatedQueryBuilder(EntitySchema $schema, DaViQueryBuilder $queryBuilder, AggregationConfiguration $aggregationConfiguration, array $columnsBlacklist = []): void {
    $columns = $aggregationConfiguration->getProperties();
    foreach ($columns as $column => $expressionName) {
      if (!is_string($column) || array_key_exists($column, $columnsBlacklist)) {
        continue;
      }
      $queryBuilder->Select('COUNT(' . $column . ') AS ' . $expressionName);
      $queryBuilder->addSelect($column);
      $queryBuilder->addGroupBy($column);
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

        if ($handler instanceof ValueFormatterItemHandlerInterface) {
          $tableRows[$index][$columnName] = $handler->getValueRawFormatted($item, $value);
        } else {
          $tableRows[$index][$columnName] = $value;
        }
      }
    }

    return new TableData($header, $tableRows);
  }

}
