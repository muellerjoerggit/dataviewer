<?php

namespace App\DaViEntity;

use App\DaViEntity\Schema\EntitySchema;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('entity_management.entity_creator')]
interface EntityCreatorInterface {

  public function createEntity(EntitySchema $schema, string $client, array $row): EntityInterface;

  public function createMissingEntity(EntityKey $entityKey, EntitySchema $schema): EntityInterface;

  public function processRow(EntityInterface $entity, array $row): void;

}