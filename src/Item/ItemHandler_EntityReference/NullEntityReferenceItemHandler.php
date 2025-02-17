<?php

namespace App\Item\ItemHandler_EntityReference;

use App\Database\TableReferenceHandler\Attribute\TableReferenceDefinition;
use App\DataCollections\EntityKeyCollection;
use App\DaViEntity\EntityInterface;
use App\DaViEntity\EntityKey;
use App\DaViEntity\Schema\EntitySchema;
use App\EntityTypes\NullEntity\NullEntity;
use App\Item\ItemConfigurationInterface;
use App\Item\ItemInterface;
use Generator;
use App\Database\TableReferenceHandler\Attribute\TableReferenceDefinitionInterface;

class NullEntityReferenceItemHandler implements EntityReferenceItemHandlerInterface {

  public function iterateEntityKeys(EntityInterface $entity, string $property): Generator {
    return yield from [];
  }

  public function buildEntityKey($value, ItemConfigurationInterface|ItemInterface $itemConfiguration, string $client): ?EntityKey {
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

  public function getEntityKeys(EntityInterface $entity, string $property): array {
    return [];
  }

  public function buildTableReferenceConfiguration(ItemConfigurationInterface $itemConfiguration, EntitySchema $schema): TableReferenceDefinitionInterface {
    return TableReferenceDefinition::createNullTableReference('null', 'NullEntity_null');
  }

  public function getTargetEntityType(ItemConfigurationInterface $itemConfiguration): string {
    return NullEntity::ENTITY_TYPE;
  }

  public function getTargetProperty(ItemConfigurationInterface $itemConfiguration): string {
    return 'id';
  }

  public function buildEntityKeyCollection(EntityInterface $entity, string $property): EntityKeyCollection|null {
    return null;
  }

}
