<?php

namespace App\DaViEntity\Schema;

use App\DaViEntity\EntityInterface;
use App\DaViEntity\EntityTypeAttributesReader;
use App\EntityTypes\NullEntity\NullEntity;
use App\Item\ItemHandler_EntityReference\EntityReferenceItemHandlerLocator;
use App\Item\ItemHandler_EntityReference\SimpleEntityReferenceJoinInterface;
use App\Item\Property\PropertyConfiguration;

class EntityTypeSchemaRegister {

  private array $schemas = [];

  private array $paths = [];
  private array $propertyConfigFromPath = [];

  public function __construct(
    private readonly EntityTypesRegister $entityTypesRegister,
    private readonly EntitySchemaBuilder $entitySchemaBuilder,
    private readonly EntityTypeAttributesReader $entityTypeClassReader,
    private readonly EntityReferenceItemHandlerLocator $referenceItemHandlerLocator,
  ) {
    $this->buildSchema(NullEntity::ENTITY_TYPE);
  }

  private function buildSchema(string $entityType): void {
    $schemaFile = $this->entityTypesRegister->getSchemaFile($entityType);
    $entityClass = $this->entityTypesRegister->getEntityClassByEntityType($entityType);
    $this->schemas[$entityType] = $this->entitySchemaBuilder->buildSchema($schemaFile, $entityClass);
  }

  public function getPropertyConfigurationFromPath(string $path, string $entityClass): PropertyConfiguration {
    [$entityClass, $property] = $this->getEntityTypePropertyFromPath($path, $entityClass);
    $entityType = $this->entityTypesRegister->getEntityTypeByEntityClass($entityClass);

    return $this->getPropertyConfiguration($entityType, $property);
  }

  public function getPropertyConfiguration(string $entityType, string $property): PropertyConfiguration {
    return $this->getEntityTypeSchema($entityType)->getProperty($property);
  }

  private function getEntityTypePropertyFromPath(string $path, string $entityClass): array  {
    $separatorPos = strpos($path, '.');
    if ($separatorPos) {
      $pathSection = substr($path, 0, $separatorPos);
      $path = substr($path, $separatorPos + 1);
    } else {
      $pathSection = $path;
      $path = '';
    }

    $schema = $this->getSchemaFromEntityClass($entityClass);

    $itemConfiguration = $schema->getProperty($pathSection);
    if ($itemConfiguration->hasEntityReferenceHandler() && !empty($path)) {
      $handler = $this->referenceItemHandlerLocator->getEntityReferenceHandlerFromItem($itemConfiguration);

      if(!$handler instanceof SimpleEntityReferenceJoinInterface) {
        return [NullEntity::class, 'id'];
      }

      [$targetEntityClass, $targetProperty] = $handler->getTargetSetting($itemConfiguration);

      if (!empty($targetEntityClass) && !empty($targetProperty)) {
        return $this->getEntityTypePropertyFromPath($path, $targetEntityClass);
      }
    }

    return [$entityClass, $pathSection];
  }

  public function getSchemaFromEntityClass(string | EntityInterface $entityClass): EntitySchema {
    $entityType = $this->entityTypeClassReader->getEntityType($entityClass);

    if($entityType === NullEntity::ENTITY_TYPE && $this->hasEntitySchema($entityClass)) {
      $entityType = $entityClass;
    }

    return $this->getEntityTypeSchema($entityType);
  }

  public function getEntityTypeSchema(string $entityType): EntitySchema {
    if (!$this->hasEntitySchema($entityType)) {
      $entityType = NullEntity::ENTITY_TYPE;
    }

    if (!isset($this->schemas[$entityType])) {
      $this->buildSchema($entityType);
    }

    return $this->schemas[$entityType];
  }

  public function hasEntitySchema(string $entityType): bool {
    if (isset($this->schemas[$entityType]) && $this->schemas[$entityType] instanceof EntitySchema) {
      return TRUE;
    }

    if ($this->entityTypesRegister->hasEntityType($entityType)) {
      return TRUE;
    }

    return FALSE;
  }

//  public function getItemConfigurationFromPath(string $path): ItemConfigurationInterface|bool {
//    return $this->propertyConfigFromPath[$path] ?? FALSE;
//  }

}