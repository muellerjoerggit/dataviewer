<?php

namespace App\Item\ItemHandler_EntityReference;

use App\DaViEntity\EntityInterface;
use App\DaViEntity\EntityKey;
use App\Item\ItemConfigurationInterface;
use App\Item\ItemInterface;
use Generator;

class NullEntityReferenceItemHandler implements EntityReferenceItemHandlerInterface {

  public function iterateEntityKeys(EntityInterface $entity, string $property): Generator {
    return yield from [];
  }

  public function buildEntityKeys($value, ItemConfigurationInterface|ItemInterface $itemConfiguration, string $client): ?EntityKey {
    return NULL;
  }

  public function getEntityLabel($entityKey): string {
    return 'Null Entity';
  }

  public function getEntityOverview($entityKey, array $options = []): array {
    return [];
  }

  public function getLabelFromValue(ItemConfigurationInterface $itemConfiguration, $value, string $client): string {
    return 'Null Entity';
  }

  public function getTargetEntityTypes(ItemConfigurationInterface $itemConfiguration): array {
    return ['NullEntity'];
  }

  public function getEntityKeys(EntityInterface $entity, string $property): array {
    return [];
  }

}
