<?php

namespace App\Database\AggregationHandler;

use App\Database\Aggregation\AggregationHandlerInterface;
use App\Database\AggregationHandler\Attribute\AggregationDefinitionInterface;
use App\Database\AggregationHandler\Attribute\CountGroupAggregationHandlerDefinition;
use App\Database\DaViQueryBuilder;
use App\Database\Exceptions\NotJoinableException;
use App\Database\TableJoinBuilder;
use App\DataCollections\TableData;
use App\DaViEntity\Schema\EntitySchema;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use App\Item\ItemHandler_EntityReference\EntityReferenceItemHandlerInterface;
use App\Item\ItemHandler_EntityReference\EntityReferenceItemHandlerLocator;
use App\Item\ItemHandler_Formatter\FormatterItemHandlerInterface;
use App\Item\ItemHandler_Formatter\FormatterItemHandlerLocator;

class CountGroupAggregationHandler extends AbstractAggregationHandler {

  protected const string COUNT_COLUMN = 'count_column';

  public function __construct(
    protected readonly EntityTypeSchemaRegister $schemaRegister,
    protected readonly FormatterItemHandlerLocator $formatterHandlerLocator,
    protected readonly EntityReferenceItemHandlerLocator $entityReferenceItemHandlerLocator,
    protected readonly TableJoinBuilder $tableJoinBuilder,
  ) {}

  public function buildAggregatedQueryBuilder(EntitySchema $schema, DaViQueryBuilder $queryBuilder, AggregationDefinitionInterface $aggregationDefinition, array $options = []): void {
    if(!$this->isValidAggregationDefinition($aggregationDefinition)) {
      return;
    }

    $options = $this->mergeDefaultOptions($options);
    $properties = $aggregationDefinition->getProperties();
    $queryBuilder->select('COUNT(*) as ' . self::COUNT_COLUMN);

    $queryBuilder->resetGroupBy();
    foreach ($properties as $property => $expressionName) {
      if(in_array($property, $options[AggregationHandlerInterface::OPTION_PROPERTY_BLACKLIST])) {
        continue;
      }

      if(str_contains($property, '.')) {
        try {
          $this->tableJoinBuilder->joinFromPropertyPath($queryBuilder, $schema, $property);
          $propertyItem = $this->schemaRegister->getPropertyConfigurationFromPath($property, $schema->getEntityType());
        } catch (NotJoinableException $exception) {
          continue;
        }
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

  public function processingAggregatedData(DaViQueryBuilder $queryBuilder, EntitySchema $schema, AggregationDefinitionInterface $aggregationDefinition): TableData | int {
    if(!$this->isValidAggregationDefinition($aggregationDefinition)) {
      return $this->createEmptyTableData();
    }

    /** @var $aggregationDefinition CountGroupAggregationHandlerDefinition */

    $data = $this->executeQueryBuilder($queryBuilder);

    $headerColumns = $aggregationDefinition->getHeader();
    $headerColumns[self::COUNT_COLUMN] = $aggregationDefinition->getLabelCountColumn();

    $aggregatedColumns = $aggregationDefinition->getProperties();
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

  protected function isValidAggregationDefinition(AggregationDefinitionInterface $aggregationDefinition): bool {
    return $aggregationDefinition instanceof CountGroupAggregationHandlerDefinition;
  }

}
