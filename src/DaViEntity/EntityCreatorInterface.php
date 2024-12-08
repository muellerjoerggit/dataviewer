<?php

namespace App\DaViEntity;

use App\DaViEntity\Schema\EntitySchema;

interface EntityCreatorInterface {

  public function createEntity(EntitySchema $schema, string $client, array $row): EntityInterface;

  public function createMissingEntity(EntityKey $entityKey, EntitySchema $schema): EntityInterface;

}