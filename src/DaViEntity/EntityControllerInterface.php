<?php

namespace App\DaViEntity;

use App\Database\Aggregation\AggregationConfiguration;
use App\Database\SqlFilter\FilterContainer;
use App\DataCollections\EntityList;
use App\DataCollections\TableData;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('entity_management.entity_controller')]
interface EntityControllerInterface {

  public function loadEntityData(FilterContainer $filterContainer, array $options = []): array;

  public function loadMultipleEntities(FilterContainer $filterContainer, array $options = []): array;

  public function loadEntityByEntityKey(EntityKey $entityKey): EntityInterface;

  public function loadAggregatedData(string $client, AggregationConfiguration $aggregation, FilterContainer $filterContainer = NULL): array|TableData;

  public function preRenderEntity(EntityInterface $entity): array;

  public function getExtendedEntityOverview(EntityInterface $entity, $options): array;

  public function getEntityOverview(EntityInterface $entity, array $options = []): array;

  public function getEntityList(FilterContainer $filterContainer): EntityList;

  public function getEntityLabel(EntityInterface $entity): string;

}