<?php

namespace App\Item\ItemHandler_EntityReference;

use App\Database\TableReferenceHandler\Attribute\TableReferenceDefinition;
use App\DataCollections\EntityKeyCollection;
use App\DaViEntity\EntityInterface;
use App\DaViEntity\EntityKey;
use App\DaViEntity\Schema\EntitySchema;
use App\EntityTypes\NullEntity\NullEntity;
use App\Item\ItemConfigurationInterface;
use App\Item\ItemHandler_EntityReference\Attribute\EntityReferenceItemHandlerDefinitionInterface;
use App\Item\ItemInterface;
use Generator;
use App\Database\TableReferenceHandler\Attribute\TableReferenceDefinitionInterface;

class NullEntityReferenceItemHandler implements EntityReferenceItemHandlerInterface {

  public function buildEntityKey($value, EntityReferenceItemHandlerDefinitionInterface $referenceDefinition, string $client): ?EntityKey {
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

  public function buildTableReferenceDefinition(ItemConfigurationInterface $itemConfiguration, EntitySchema $schema): TableReferenceDefinitionInterface {
    return TableReferenceDefinition::createNullTableReference('null', 'NullEntity_null');
  }

  public function buildEntityKeyCollection(EntityInterface $entity, string $property): EntityKeyCollection|null {
    return null;
  }

  public function getTargetSetting(EntityReferenceItemHandlerDefinitionInterface|ItemConfigurationInterface $referenceDefinition): array {
    return [NullEntity::class, 'id'];
  }

}
