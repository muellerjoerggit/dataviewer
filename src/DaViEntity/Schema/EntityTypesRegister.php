<?php

namespace App\DaViEntity\Schema;

use Generator;
use Symfony\Component\Finder\SplFileInfo;

class EntityTypesRegister {

  private array $entityTypes = [];

  private array $entityClasses = [];

  public function __construct(
    EntityTypesReader $entityTypesReader
  ) {
    $this->entityTypes = $entityTypesReader->read();
    $this->entityClasses = array_column($this->entityTypes, EntityTypesReader::KEY_ENTITY_TYPE, EntityTypesReader::KEY_ENTITY_CLASS);
  }

  public function getSchemaFile(string $type): SplFileInfo {
    return $this->entityTypes[$type][EntityTypesReader::KEY_SCHEMA_FILE];
  }

  public function getEntityClassByEntityType(string $entityType): string {
    return $this->entityTypes[$entityType][EntityTypesReader::KEY_ENTITY_CLASS];
  }

  public function getEntityTypeByEntityClass(string $entityClass): string {
    return $this->entityClasses[$entityClass];
  }

  public function hasEntityType(string $type): bool {
    return array_key_exists($type, $this->entityTypes);
  }

  private function hasEntityClass(string $className): bool {
    return array_key_exists($className, $this->entityClasses);
  }

  public function iterateErrorCodesFiles(): Generator {
    foreach ($this->entityTypes as $type => $entities) {
      yield $type => $this->getErrorCodeFile($type);
    }
  }

  public function getErrorCodeFile(string $type): SplFileInfo {
    return $this->entityTypes[$type][EntityTypesReader::KEY_ERROR_CODE_FILE];
  }

  public function iterateEntityTypes(): Generator {
    foreach ($this->entityTypes as $type => $entities) {
      yield $type;
    }
  }

}
