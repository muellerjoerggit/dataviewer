<?php

namespace App\Item\ItemHandler_EntityReference;

use App\Database\DaViQueryBuilder;
use App\DaViEntity\Schema\EntitySchema;
use App\Item\ItemConfigurationInterface;

interface SimpleEntityReferenceJoinInterface {

  public function joinTable(DaViQueryBuilder $queryBuilder, ItemConfigurationInterface $itemConfiguration, EntitySchema $fromSchema, bool $innerJoin = false): void;

}