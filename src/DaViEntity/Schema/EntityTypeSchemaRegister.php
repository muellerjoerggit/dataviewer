<?php

namespace App\DaViEntity\Schema;

use App\DaViEntity\EntityTypeAttributesReader;
use App\Item\ItemConfigurationInterface;

class EntityTypeSchemaRegister {

  private array $schemas = [];

  private array $paths = [];

  private array $propertyConfigFromPath = [];

  public function __construct(
    private readonly EntityTypesRegister $entityTypesRegister,
    private readonly EntitySchemaBuilder $entitySchemaBuilder,
    private readonly EntityTypeAttributesReader $entityTypeClassReader,
  ) {
    $this->buildSchema('NullEntity');
  }

  private function buildSchema(string $entityType): void {
    $schemaFile = $this->entityTypesRegister->getSchemaFile($entityType);
    $this->schemas[$entityType] = $this->entitySchemaBuilder->buildSchema($schemaFile);
  }

  public function resolvePath(string $path, string $entityType, $originallyPath = '') {
    $ret = [];

    if (isset($this->paths[$path])) {
      return $this->paths[$path];
    }

    if (empty($originallyPath)) {
      $originallyPath = $path;
    }

    $separatorPos = strpos($path, '.');
    if ($separatorPos) {
      $pathSection = substr($path, 0, $separatorPos);
      $path = substr($path, $separatorPos + 1);
    } else {
      $pathSection = $path;
      $path = '';
    }

    $schema = $this->getEntityTypeSchema($entityType);

    $itemConfiguration = $schema->getProperty($pathSection);
    if ($itemConfiguration->hasEntityReferenceHandler() && !empty($path)) {
      $entityReferenceSetting = $itemConfiguration->getEntityReferenceHandlerSetting();
      $targetEntityType = $entityReferenceSetting['target_entity_type'] ?? '';
      $targetProperty = $entityReferenceSetting['target_property'] ?? '';

      if (empty($targetEntityType) || empty($targetProperty)) {
        return $ret;
      }

      $targetSchema = $this->getEntityTypeSchema($targetEntityType);

      $ret[$entityType] = [
        'source_table' => $schema->getBaseTable(),
        'target_table' => $targetSchema->getBaseTable(),
        'source_property' => $pathSection,
        'target_property' => $targetProperty,
      ];

      $ret = array_merge($this->resolvePath($path, $targetEntityType, $originallyPath), $ret);
    } else {
      $ret[$entityType]['column'] = $pathSection;
      $ret[$entityType]['source_table'] = $schema->getBaseTable();
      $this->propertyConfigFromPath[$originallyPath] = $itemConfiguration;
    }

    $this->paths[$originallyPath] = $ret;

    return $ret;
  }

  public function getSchemaFromEntityClass(string $entityClass): EntitySchema {
    return $this->getEntityTypeSchema($this->entityTypeClassReader->getEntityType($entityClass));
  }

  public function getEntityTypeSchema(string $entityType): EntitySchema {
    if (!$this->hasEntitySchema($entityType)) {
      $entityType = 'NullEntity';
    }

    if (!isset($this->schemas[$entityType])) {
      $this->buildSchema($entityType);
    }

    return $this->schemas[$entityType];
  }

  public function hasEntitySchema(string $entityType): bool {
    if (isset($this->schemas[$entityType])) {
      return TRUE;
    }

    if ($this->entityTypesRegister->hasEntityType($entityType)) {
      return TRUE;
    }

    return FALSE;
  }

  public function getItemConfigurationFromPath(string $path): ItemConfigurationInterface|bool {
    return $this->propertyConfigFromPath[$path] ?? FALSE;
  }

}