<?php

namespace App\Database\AggregationHandler;

use App\Database\Aggregation\AggregationConfiguration;
use App\Database\DaViQueryBuilder;
use App\DataCollections\TableData;
use App\DaViEntity\Schema\EntitySchema;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use App\Item\ItemConfigurationInterface;
use App\Item\ItemHandler_EntityReference\EntityReferenceItemHandlerInterface;
use App\Item\ItemHandler_EntityReference\EntityReferenceItemHandlerLocator;
use App\Item\ItemHandler_ValueFormatter\ValueFormatterItemHandlerInterface;
use App\Item\ItemHandler_ValueFormatter\ValueFormatterItemHandlerLocator;

/**
 * Configuration example:
 * <code>
 *   count_users_status:
 *    title: "Number of users by status"
 *    description: "Number of users with the respective role grouped by user
 * status (active or inactive)" handler: CountGroupAggregationHandler:
 * properties: rol_id: "Role id" usr_id.active: "Active"
 * </code>
 */
class CountGroupAggregationHandler extends AbstractAggregationHandler {

  public function __construct(
    private readonly EntityTypeSchemaRegister $schemaRegister,
    private readonly ValueFormatterItemHandlerLocator $formatterHandlerLocator,
    private readonly EntityReferenceItemHandlerLocator $entityReferenceItemHandlerLocator
  ) {}

  public function buildAggregatedQueryBuilder(EntitySchema $schema, DaViQueryBuilder $queryBuilder, AggregationConfiguration $aggregationConfiguration, array $columnsBlacklist = []): void {
    $columns = $aggregationConfiguration->getProperties();
    $header = $aggregationConfiguration->getSetting('header');

    $columnCount = $header['count_column'] ?? 'count_column';
    $queryBuilder->select('COUNT(*) as ' . $columnCount);

    $joins = [];
    $queryBuilder->resetGroupBy();
    $baseTable = $schema->getBaseTable();
    foreach ($columns as $column => $expressionName) {
      if (!is_string($column) || array_key_exists($column, $columnsBlacklist)) {
        continue;
      }
      if (str_contains($column, '.')) {
        $joins = array_merge($this->schemaRegister->resolvePath($column, $schema->getEntityType()), $joins);
        continue;
      }

      $queryBuilder->addSelect($baseTable . '.' . $column . ' as ' . $expressionName);
      $queryBuilder->addGroupBy($baseTable . '.' . $column);
    }

    foreach ($joins as $join) {
      $sourceTable = $join['source_table'] ?? '';
      $targetTable = $join['target_table'] ?? '';
      $sourceProperty = $join['source_property'] ?? '';
      $targetProperty = $join['target_property'] ?? '';
      $column = $join['column'] ?? '';

      if (!empty($column) && !empty($sourceTable)) {
        $queryBuilder->addSelect($sourceTable . '.' . $column);
        $queryBuilder->addGroupBy($sourceTable . '.' . $column);
      }

      if (empty($sourceTable) || empty($targetTable) || empty($sourceProperty) || empty($targetProperty)) {
        continue;
      }

      $condition = $sourceTable . '.' . $sourceProperty . ' = ' . $targetTable . '.' . $targetProperty;

      $queryBuilder->Join($sourceTable, $targetTable, $targetTable, $condition);
    }
  }

  public function processingAggregatedData(DaViQueryBuilder $queryBuilder, EntitySchema $schema, AggregationConfiguration $aggregationConfiguration): mixed {
    $data = $this->executeQueryBuilder($queryBuilder);

    $headerColumns = $aggregationConfiguration->getSetting('header');
    if (!isset($headerColumns['count_column'])) {
      $headerColumns['count_column'] = 'Anzahl';
    }
    $aggregatedColumns = $aggregationConfiguration->getProperties();
    $items = [];
    foreach ($aggregatedColumns as $column => $columnName) {
      if (!$schema->hasProperty($column) && !$this->schemaRegister->getItemConfigurationFromPath($column)) {
        continue;
      }
      $config = $schema->hasProperty($column) ? $schema->getProperty($column) : $this->schemaRegister->getItemConfigurationFromPath($column);

      if (!($config instanceof ItemConfigurationInterface)) {
        continue;
      }

      $items[$columnName] = $config;
    }

    $header = [];
    $tableRows = [];

    foreach ($data as $index => $row) {
      foreach ($row as $columnName => $value) {
        $handler = NULL;
        if (!isset($header[$columnName])) {
          $header[$columnName] = $headerColumns[$columnName] ?? $columnName;
        }

        $itemConfiguration = $items[$columnName] ?? FALSE;
        if ($itemConfiguration) {
          if ($itemConfiguration->hasEntityReferenceHandler()) {
            $handler = $this->entityReferenceItemHandlerLocator->getEntityReferenceHandlerFromItem($itemConfiguration);
          } elseif ($itemConfiguration->hasFormatterHandler()) {
            $handler = $this->formatterHandlerLocator->getFormatterHandlerFromItem($itemConfiguration);
          }
        }

        if ($handler instanceof ValueFormatterItemHandlerInterface) {
          $tableRows[$index][$columnName] = $handler->getValueRawFormatted($itemConfiguration, $value);
        } elseif ($handler instanceof EntityReferenceItemHandlerInterface) {
          $tableRows[$index][$columnName] = $handler->getLabelFromValue($itemConfiguration, $value, $queryBuilder->getClient());
        } else {
          $tableRows[$index][$columnName] = $value;
        }
      }
    }

    return new TableData($header, $tableRows);
  }

}
