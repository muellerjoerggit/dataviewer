<?php

namespace App\Item\ItemHandler_EntityReference;

use App\DaViEntity\EntityInterface;
use App\DaViEntity\EntityKey;
use App\Item\ItemConfigurationInterface;
use Generator;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('entity_reference_item_handler')]
interface EntityReferenceItemHandlerInterface {

  public function getEntityLabel($entityKey): string;

  public function getEntityOverview($entityKey, array $options = []): array;

  /**
   * @return \Generator <EntityKey>
   */
  public function iterateEntityKeys(EntityInterface $entity, string $property): Generator;

  public function getEntityKeys(EntityInterface $entity, string $property): array;

  public function buildEntityKeys($value, ItemConfigurationInterface $itemConfiguration, string $client): ?EntityKey;

}
