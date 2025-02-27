<?php

namespace App\Database\AggregationHandler;

use App\Database\Aggregation\AggregationConfiguration;
use App\Database\Aggregation\AggregationHandlerInterface;
use App\Database\QueryBuilder\DaViQueryBuilder;
use App\Database\QueryBuilder\QueryBuilderInterface;
use App\Database\TableReference\TableJoinBuilder;
use App\DataCollections\TableData;
use App\DaViEntity\Schema\EntitySchema;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use App\Item\ItemHandler_EntityReference\EntityReferenceItemHandlerInterface;
use App\Item\ItemHandler_EntityReference\EntityReferenceItemHandlerLocator;
use App\Item\ItemHandler_Formatter\FormatterItemHandlerInterface;
use App\Item\ItemHandler_Formatter\FormatterItemHandlerLocator;

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
    private readonly FormatterItemHandlerLocator $formatterHandlerLocator,
    private readonly EntityReferenceItemHandlerLocator $entityReferenceItemHandlerLocator,
    private readonly TableJoinBuilder $tableJoinBuilder,
  ) {}

  public function buildAggregatedQueryBuilder(EntitySchema $schema, QueryBuilderInterface $queryBuilder, AggregationConfiguration $aggregationConfiguration, array $options = []): void {
    $properties = $aggregationConfiguration->getProperties();
    $blackList = $options[AggregationHandlerInterface::YAML_PARAM_PROPERTY_BLACKLIST] ?? [];
    $queryBuilder->select('COUNT(*) as ' . AggregationHandlerInterface::YAML_PARAM_COUNT_COLUMN);

    $queryBuilder->resetGroupBy();
    foreach ($properties as $property => $expressionName) {
      if(in_array($property, $blackList, true)) {
        continue;
      }

      if(str_contains($property, '.')) {
        $this->tableJoinBuilder->joinFromPropertyPath($queryBuilder, $schema, $property);
        $propertyItem = $this->schemaRegister->getPropertyConfigurationFromPath($property, $schema->getEntityType());
      } else {
        $propertyItem = $schema->getProperty($property);
      }

      $column = $propertyItem->getColumn();

      if(empty($column)) {
        continue;
      }

      $queryBuilder->addSelect($column . ' AS ' . $expressionName);
      $queryBuilder->addGroupBy($column);
    }

  }

  public function processingAggregatedData(QueryBuilderInterface $queryBuilder, EntitySchema $schema, AggregationConfiguration $aggregationConfiguration): mixed {
    $data = $this->executeQueryBuilder($queryBuilder);

    $headerColumns = $aggregationConfiguration->getSetting('header');
    $headerColumns[AggregationHandlerInterface::YAML_PARAM_COUNT_COLUMN] = $headerColumns[AggregationHandlerInterface::YAML_PARAM_COUNT_COLUMN] ?? 'Anzahl';

    $aggregatedColumns = $aggregationConfiguration->getProperties();
    $items = [];
    foreach ($aggregatedColumns as $property => $propertyKey) {
      if(str_contains($property, '.')) {
        $propertyConfig = $this->schemaRegister->getPropertyConfigurationFromPath($property, $schema->getEntityType());
      } else {
        $propertyConfig = $schema->getProperty($property);
      }

      $items[$propertyKey] = $propertyConfig;
    }

    $header = [];
    $tableRows = [];

    foreach ($data as $index => $row) {
      foreach ($row as $propertyKey => $value) {
        $handler = NULL;
        if (!isset($header[$propertyKey])) {
          $header[$propertyKey] = $headerColumns[$propertyKey] ?? $propertyKey;
        }

        $itemConfiguration = $items[$propertyKey] ?? FALSE;
        if ($itemConfiguration) {
          if ($itemConfiguration->hasEntityReferenceHandler()) {
            $handler = $this->entityReferenceItemHandlerLocator->getEntityReferenceHandlerFromItem($itemConfiguration);
          } elseif ($itemConfiguration->hasFormatterHandler()) {
            $handler = $this->formatterHandlerLocator->getFormatterHandlerFromItem($itemConfiguration);
          }
        }

        if ($handler instanceof FormatterItemHandlerInterface) {
          $tableRows[$index][$propertyKey] = $handler->getValueRawFormatted($itemConfiguration, $value);
        } elseif ($handler instanceof EntityReferenceItemHandlerInterface) {
          $tableRows[$index][$propertyKey] = $handler->getLabelFromValue($itemConfiguration, $value, $queryBuilder->getClient());
        } else {
          $tableRows[$index][$propertyKey] = $value;
        }
      }
    }

    return new TableData($header, $tableRows);
  }

}
