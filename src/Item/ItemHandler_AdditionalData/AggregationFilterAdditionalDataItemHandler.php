<?php

namespace App\Item\ItemHandler_AdditionalData;

use App\Database\Aggregation\AggregationHandlerInterface;
use App\Database\SqlFilter\FilterContainer;
use App\Database\SqlFilter\SqlFilter;
use App\Database\SqlFilter\SqlFilterBuilder;
use App\DataCollections\TableData;
use App\DaViEntity\DaViEntityManager;
use App\DaViEntity\EntityInterface;
use App\DaViEntity\Schema\EntitySchema;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use App\Item\ItemHandler_AdditionalData\Attribute\AggregationAdditionalDataHandlerDefinition;

class AggregationFilterAdditionalDataItemHandler implements AdditionalDataItemHandlerInterface {

  public function __construct(
    private readonly DaViEntityManager $entityManager,
    private readonly EntityTypeSchemaRegister $schemaRegister,
  ) {}

  public function getValues(EntityInterface $entity, string $property): TableData | array | int {
    $itemConfiguration = $entity->getPropertyItem($property)->getConfiguration();
    $definition = $itemConfiguration->getAdditionalDataHandlerDefinition();

    if(!$definition instanceof AggregationAdditionalDataHandlerDefinition) {
      return [];
    }

    $targetEntityClass = $definition->getTargetEntityClass();
    $targetSchema = $this->schemaRegister->getSchemaFromEntityClass($targetEntityClass);
    $options = [];

    if($definition->hasPropertyBlacklist()) {
      $options[AggregationHandlerInterface::OPTION_PROPERTY_BLACKLIST] = $definition->getPropertyBlacklist();
    }

    $filterContainer = $this->prepareFilterContainer($targetSchema, $definition, $entity);
    $aggregationDefinition = $targetSchema->getAggregation($definition->getAggregationKey());
    return $this->entityManager->loadAggregatedData($targetEntityClass, $entity->getClient(), $aggregationDefinition, $filterContainer, $options);
  }

  protected function prepareFilterContainer(EntitySchema $targetSchema, AggregationAdditionalDataHandlerDefinition $definition, EntityInterface $entity): FilterContainer {
    $filterSettings = $definition->getFilters();
    $filterContainer = SqlFilterBuilder::buildDefaultFilterContainerAndAppend($entity->getClient(), $targetSchema);
    foreach ($filterSettings as $filterSetting) {
      $filterDefinition = $targetSchema->getFilterDefinition($filterSetting['filter']);

      if(!$filterDefinition) {
        continue;
      }

      $sourceValues = $entity->getPropertyItem($filterSetting['filterMapping'])->getValuesAsArray();
      $filter = new SqlFilter($filterDefinition, $sourceValues, 'AggregationFilterAdditionalDataItemHandler');
      $filterContainer->addFilters($filter);
    }
    return $filterContainer;
  }

}
