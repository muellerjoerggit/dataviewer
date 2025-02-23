<?php

namespace App\EntityServices\AggregatedData;

use App\Database\AggregationHandler\Attribute\AggregationDefinitionInterface;
use App\Database\SqlFilter\FilterContainer;
use App\DataCollections\TableData;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('entity_management.aggregated_data_provider')]
interface AggregatedDataProviderInterface {

  public function fetchAggregatedData(
    string $entityClass,
    string $client,
    AggregationDefinitionInterface $aggregationDefinition,
    FilterContainer $filterContainer = null,
    array $options = []
  ): TableData;

}