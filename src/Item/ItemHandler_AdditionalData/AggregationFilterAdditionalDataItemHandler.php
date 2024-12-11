<?php

namespace App\Item\ItemHandler_AdditionalData;

use App\Database\SqlFilter\FilterContainer;
use App\Database\SqlFilter\SqlFilter;
use App\Database\SqlFilter\SqlFilterBuilder;
use App\DataCollections\TableData;
use App\DaViEntity\DaViEntityManager;
use App\DaViEntity\EntityInterface;
use App\DaViEntity\Schema\EntitySchema;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use App\Item\ItemConfigurationInterface;

class AggregationFilterAdditionalDataItemHandler implements AdditionalDataItemHandlerInterface {

  public function __construct(
    private readonly DaViEntityManager $daViEntityManager,
    private readonly EntityTypeSchemaRegister $schemaRegister,
  ) {}

  public function getValues(EntityInterface $entity, string $property): TableData|array {
    $itemConfiguration = $entity->getPropertyItem($property)
      ->getConfiguration();
    $targetEntityType = $itemConfiguration->getTargetEntityTypeLazyLoader();
    $aggregationSettings = $itemConfiguration->getLazyLoaderSettings()['aggregation'] ?? [];
    $columnsBlacklist = $aggregationSettings['columns_blacklist'] ?? [];
    $aggregationKey = $aggregationSettings['key'] ?? '';
    if (empty($aggregationKey) || empty($targetEntityType)) {
      return [];
    }
    $schema = $this->schemaRegister->getEntityTypeSchema($targetEntityType);
    $filterContainer = $this->prepareFilterContainer($schema, $itemConfiguration, $entity);
    $data = $this->daViEntityManager->loadAggregatedEntityData($targetEntityType, $entity->getClient(), $aggregationKey, $filterContainer, $columnsBlacklist);
    return $data;
  }

  protected function prepareFilterContainer(EntitySchema $schema, ItemConfigurationInterface $itemConfiguration, EntityInterface $entity): FilterContainer {
    $filterSettings = $itemConfiguration->getAdditionalDataSetting()['filters'] ?? [];
    $filterContainer = SqlFilterBuilder::buildDefaultFilterContainerAndAppend($entity->getClient(), $schema);
    foreach ($filterSettings as $key => $filterSetting) {
      $filterDefinition = $schema->getFilterDefinition($filterSetting['filter']);
      $sourceValues = $entity->getPropertyItem($filterSetting['filter_mapping'])
        ->getValuesAsOneDimensionalArray();
      $filter = new SqlFilter($filterDefinition, $sourceValues);
      $filterContainer->addFilters($filter);
    }
    return $filterContainer;
  }

}
