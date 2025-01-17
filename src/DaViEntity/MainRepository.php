<?php

namespace App\DaViEntity;

use App\EntityTypes\NullEntity\NullEntity;

class MainRepository {

  public const string INDEX_ALL_KEYS = 'all_keys';
  public const string INDEX_ENTITY_TYPE = 'entity_type';
  public const string INDEX_ERROR_MISSING = 'missing_error_entities';
  private const int MAX_CACHED_ENTITIES = 500;

  private array $entities = [];

  private array $keys = [
    self::INDEX_ALL_KEYS => [],
    self::INDEX_ENTITY_TYPE => [],
    self::INDEX_ERROR_MISSING => [],
  ];

  public function addEntity(EntityInterface $entity): void {
    if (($this->entityExists($entity))) {
      return;
    }

    $keys = $this->resolveEntityKeys($entity);
    if (empty($keys)) {
      return;
    }

    $firstKey = array_shift($keys);

    $this->entities[] = $entity;
    $last = array_key_last($this->entities);

    $this->addEntityKey($firstKey, $last, self::INDEX_ENTITY_TYPE, $entity->getEntityType());
    $this->addEntityKey($firstKey, $last, self::INDEX_ALL_KEYS);
    if ($entity->isMissingEntity() || $entity instanceof NullEntity) {
      $this->addEntityKey($firstKey, $last, self::INDEX_ERROR_MISSING);
    }

    foreach ($keys as $key) {
      $this->addEntityKey($key, $last, self::INDEX_ALL_KEYS);
    }

    if (count($this->entities) > self::MAX_CACHED_ENTITIES) {
      $removedEntity = array_pop($this->entities);
      $removedKeys = $this->resolveEntityKeys($removedEntity);
      foreach ($removedKeys as $removedKey) {
        unset($this->keys[self::INDEX_ALL_KEYS][$removedKey]);
        unset($this->keys[self::INDEX_ERROR_MISSING][$removedKey]);
        unset($this->keys[self::INDEX_ENTITY_TYPE][$removedEntity->getEntityType()][$removedKey]);
      }
    }
  }

  private function addEntityKey(string $key, int $index, string $indexName, string $subCategory = ''): void {
    if (empty($subCategory)) {
      $this->keys[$indexName][$key] = $index;
    } else {
      $this->keys[$indexName][$subCategory][$key] = $index;
    }
  }

  public function entityExists($key): bool {
    $keyList = $this->resolveEntityKeys($key);

    foreach ($keyList as $key) {
      if (array_key_exists($key, $this->keys[self::INDEX_ALL_KEYS])) {
        return TRUE;
      }
    }

    return FALSE;
  }

  public function resolveEntityKeys($input): array {
    if ($input instanceof EntityInterface) {
      $input = $input->getEntityKeyAsObj();
    }

    if (is_string($input)) {
      $input = EntityKey::createFromString($input);
    }

    if ($input instanceof EntityKey && $input->getEntityType() != NullEntity::ENTITY_TYPE) {
      return $input->getEntityKeysAsStrings();
    }

    return [];
  }

  public function getEntity($entityKey): EntityInterface|bool {
    if ($this->entityExists($entityKey)) {
      $entity = $this->resolveEntity($entityKey);
    }

    if (isset($entity) && $entity instanceof EntityInterface) {
      return $entity;
    }

    return FALSE;
  }

  public function resolveEntity($input) {
    $keys = $this->resolveEntityKeys($input);

    foreach ($keys as $key) {
      if (isset($this->keys[self::INDEX_ALL_KEYS][$key])) {
        $index = $this->keys[self::INDEX_ALL_KEYS][$key];
        $entity = $this->entities[$index];
        $this->entities = [$index => $entity] + $this->entities;
        return $entity;
      }
    }

    return FALSE;
  }

  public function iterateEntities(): \Generator {
    foreach ($this->entities as $key => $entity) {
      yield $key => $entity;
    }
  }

  public function iterateEntitiesByEntityType($entityType): \Generator {
    if (!isset($this->keys[self::INDEX_ENTITY_TYPE][$entityType])) {
      yield from [];
    } else {
      foreach ($this->keys[self::INDEX_ENTITY_TYPE][$entityType] as $key => $index) {
        yield $key => $this->entities[$index];
      }
    }
  }

}
