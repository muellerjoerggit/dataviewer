<?php

namespace App\Item\ItemHandler_EntityReference;

use App\Database\QueryBuilder\QueryBuilderInterface;
use App\DaViEntity\Schema\EntitySchema;
use App\Item\ItemConfigurationInterface;
use App\Item\ItemHandler_EntityReference\Attribute\EntityReferenceItemHandlerDefinitionInterface;

/**
 * Reference item handler, where a column only represents one reference to another table.
 * Don't use this interface, when a column contains several different foreign keys dependent on other columns.
 */
interface SimpleEntityReferenceJoinInterface extends EntityReferenceItemHandlerInterface {

  public function joinTable(QueryBuilderInterface $queryBuilder, ItemConfigurationInterface $itemConfiguration, EntitySchema $fromSchema, bool $innerJoin = false): void;

  public function getTargetSetting(EntityReferenceItemHandlerDefinitionInterface | ItemConfigurationInterface $referenceDefinition): array;

}