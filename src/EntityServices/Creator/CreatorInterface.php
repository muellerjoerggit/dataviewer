<?php

namespace App\EntityServices\Creator;

use App\DaViEntity\EntityInterface;
use App\DaViEntity\EntityKey;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('entity_management.entity_creator')]
interface CreatorInterface {

  public function createEntity(string $entityClass, string $client, array $row): EntityInterface;

  public function createMissingEntity(EntityKey $entityKey): EntityInterface;

  public function processRow(EntityInterface $entity, array $row): void;

}