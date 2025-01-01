<?php

namespace App\DaViEntity;

use App\DaViEntity\Schema\EntitySchema;
use App\DaViEntity\Schema\EntityTypesRegister;
use App\Item\Property\PropertyBuilder;

class CommonEntityCreator implements EntityCreatorInterface {

  public function __construct(
    private readonly EntityTypesRegister $entityTypesRegister,
    private readonly PropertyBuilder $propertyBuilder
  ) {}

  public function createEntity(EntitySchema $schema, string $client, array $row): EntityInterface {
    $entity = $this->createEntityObject($schema, $client);
    $this->processRow($entity, $row);
    return $entity;
  }

  public function processRow(EntityInterface $entity, array $row): void {
    $schema = $entity->getSchema();
    foreach ($row as $column => $value) {
      if (!$schema->hasProperty($column)) {
        continue;
      }

      $this->addProperty($schema, $entity, $column, $value);
    }
  }

  protected function createEntityObject(EntitySchema $schema, string $client): EntityInterface {
    $class = $this->entityTypesRegister->getEntityClassByEntityType($schema->getEntityType());
    return new $class($schema, $client);
  }

  protected function addProperty(EntitySchema $schema, EntityInterface $entity, string $property, mixed $value): void {
    $itemConfig = $schema->getProperty($property);

    $item = $this->propertyBuilder->createProperty($itemConfig, $value);
    $entity->setPropertyItem($property, $item);
  }

  public function createMissingEntity(EntityKey $entityKey, EntitySchema $schema): EntityInterface {
    $entity = $this->createEntityObject($schema, $entityKey->getClient());
    $uniqueIdentifiers = $entityKey->getUniqueIdentifiers();

    foreach ($uniqueIdentifiers as $uniqueIdentifier) {
      foreach ($uniqueIdentifier->iterateIdentifier() as $property => $id) {
        if (!$schema->hasProperty($property)) {
          continue;
        }

        $this->addProperty($schema, $entity, $property, $id);
      }
    }

    $entity->setMissingEntity(TRUE);

    return $entity;
  }

}