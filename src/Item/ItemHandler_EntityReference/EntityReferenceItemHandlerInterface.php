<?php

namespace App\Item\ItemHandler_EntityReference;

use App\Database\TableReferenceHandler\Attribute\TableReferenceDefinitionInterface;
use App\DataCollections\EntityKeyCollection;
use App\DaViEntity\EntityInterface;
use App\DaViEntity\EntityKey;
use App\DaViEntity\Schema\EntitySchema;
use App\Item\ItemConfigurationInterface;
use App\Item\ItemHandler_EntityReference\Attribute\EntityReferenceItemHandlerDefinitionInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('entity_reference_item_handler')]
interface EntityReferenceItemHandlerInterface {

	public function getEntityLabel($entityKey): string;

	public function getEntityOverview($entityKey, array $options = []): array;

  public function buildEntityKey($value, EntityReferenceItemHandlerDefinitionInterface $referenceDefinition, string $client): ?EntityKey;

  public function getTargetSetting(EntityReferenceItemHandlerDefinitionInterface | ItemConfigurationInterface $referenceDefinition): array;

  public function buildEntityKeyCollection(EntityInterface $entity, string $property): EntityKeyCollection | null;

}
