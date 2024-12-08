<?php

namespace App\DaViEntity\Schema;

use App\DaViEntity\EntityControllerInterface;
use App\DaViEntity\EntityControllerLocator;
use App\DaViEntity\EntityInterface;
use App\DaViEntity\EntityKey;
use App\DaViEntity\EntityTypes\NullEntity\NullEntity;
use Symfony\Component\Finder\SplFileInfo;

class EntityTypesRegister {

  private array $entityTypes = [];
  private array $entityClasses = [];

	public function __construct(
    private readonly EntityControllerLocator $controllerLocator,
    EntityTypesReader $entityTypesReader
  ) {
    $this->entityTypes = $entityTypesReader->read();
    $this->entityClasses = array_column($this->entityTypes, EntityTypesReader::KEY_ENTITY_CLASS, EntityTypesReader::KEY_ENTITY_TYPE);
	}

  public function getSchemaFile(string $type): SplFileInfo {
    return $this->entityTypes[$type][EntityTypesReader::KEY_SCHEMA_FILE];
  }

  public function getErrorCodeFile(string $type): SplFileInfo {
    return $this->entityTypes[$type][EntityTypesReader::KEY_ERROR_CODE_FILE];
  }

  public function getEntityClassByEntityType(string $entityType): string {
    return $this->entityTypes[$entityType][EntityTypesReader::KEY_ENTITY_CLASS];
  }

  public function getEntityTypeControllerClass(string $type): string {
    return $this->entityTypes[$type][EntityTypesReader::KEY_CONTROLLER_CLASS] ?? '';
  }

  public function hasEntityType(string $type): bool {
    return array_key_exists($type, $this->entityTypes);
  }

  private function hasEntityClass(string $className): bool {
    return array_key_exists($className, $this->entityClasses);
  }

  public function resolveEntityTypeFromInput($input): string {

    $fallback = NullEntity::ENTITY_TYPE;

    if(is_string($input) && $this->hasEntityType($input)) {
      return $input;
    }

    if(is_string($input) && $this->hasEntityClass($input)) {
      return $this->entityClasses[$input];
    }

    if($input instanceof EntityInterface || $input instanceof EntityKey) {
      return $input->getEntityType();
    }

    return $fallback;
  }

	public function resolveEntityController($input): EntityControllerInterface {
		$entityType = $this->resolveEntityTypeFromInput($input);
		$controllerClass = $this->getEntityTypeControllerClass($entityType);

		return $this->controllerLocator->getController($controllerClass);
	}

  public function iterateErrorCodesFiles(): \Generator {
    foreach($this->entityTypes as $type => $entities) {
      yield $type => $this->getErrorCodeFile($type);
    }
  }

  public function iterateEntityTypes(): \Generator {
    foreach($this->entityTypes as $type => $entities) {
      yield $type;
    }
  }

}
