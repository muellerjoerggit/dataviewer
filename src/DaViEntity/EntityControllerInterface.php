<?php

namespace App\DaViEntity;

use App\Database\Aggregation\AggregationConfiguration;
use App\Database\SqlFilter\FilterContainer;
use App\DataCollections\EntityList;
use App\DataCollections\TableData;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('entity_management.entity_controller')]
interface EntityControllerInterface {

  public function loadAggregatedData(string $client, AggregationConfiguration $aggregation, FilterContainer $filterContainer = NULL): array|TableData;

  public function getEntityLabel(EntityInterface $entity): string;

}