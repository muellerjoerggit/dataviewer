<?php

namespace App\Item\ItemHandler_EntityReference;

use App\Database\TableReferenceHandler\Attribute\TableReferenceDefinitionInterface;
use App\DataCollections\EntityKeyCollection;
use App\DaViEntity\EntityInterface;
use App\DaViEntity\EntityKey;
use App\DaViEntity\Schema\EntitySchema;
use App\Item\ItemConfigurationInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('entity_reference_item_handler')]
interface EntityReferenceItemHandlerInterface {

  public const string YAML_PARAM_TARGET_ENTITY_TYPE = 'targetEntityType';

	public function getEntityLabel($entityKey): string;

	public function getEntityOverview($entityKey, array $options = []): array;

  public function buildEntityKey($value, ItemConfigurationInterface $itemConfiguration, string $client): ?EntityKey;

  public function buildTableReferenceConfiguration(ItemConfigurationInterface $itemConfiguration, EntitySchema $schema): TableReferenceDefinitionInterface;

  public function getTargetEntityType(ItemConfigurationInterface $itemConfiguration): string;

  public function getTargetProperty(ItemConfigurationInterface $itemConfiguration): string;

  public function buildEntityKeyCollection(EntityInterface $entity, string $property): EntityKeyCollection | null;

}
