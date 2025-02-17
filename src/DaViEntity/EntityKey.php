<?php

namespace App\DaViEntity;

use App\EntityTypes\NullEntity\NullEntity;

class EntityKey {

  public function __construct(
    private readonly string $client,
    private readonly string $entityType,
    private array $uniqueIdentifiers
  ) {}

  public static function createNullEntityKey(): EntityKey {
    $entityType = NullEntity::ENTITY_TYPE;
    $uniqueIdentifier = (new UniqueKey())->addIdentifier('id', 0);

    return new static ('', $entityType, [$uniqueIdentifier]);
  }

  public static function createFromString(string $entityKeyString, bool $dontReturnNullEntity = FALSE): EntityKey|null {
    $keyParts = explode('::', $entityKeyString);
    $error = FALSE;

    if (!(count($keyParts) >= 4)) {
      $error = TRUE;
    }

    $properties = explode('+', $keyParts[2]);
    $values = explode('+', $keyParts[3]);

    if (count($properties) !== count($values)) {
      $error = TRUE;
    }

    $uniqueIdentifier = new UniqueKey();
    foreach ($properties as $key => $property) {
      $uniqueIdentifier->addIdentifier($property, $values[$key]);
    }

    if ($error && $dontReturnNullEntity) {
      return NULL;
    } elseif ($error) {
      return self::createNullEntityKey();
    }

    return self::create($keyParts[0], $keyParts[1], [$uniqueIdentifier]);
  }

  public static function create(string $client, string $entityType, array $uniqueIdentifiers): EntityKey {
    if (empty($entityType) || empty($uniqueIdentifiers)) {
      return self::createNullEntityKey();
    }

    return new static ($client, $entityType, $uniqueIdentifiers);
  }

  /**
   * @return UniqueKey[]
   */
  public function getUniqueIdentifiers(): array {
    return $this->uniqueIdentifiers;
  }

  private function buildEntityKeyString(UniqueKey $uniqueIdentifier): string {
    $identifierString = $uniqueIdentifier->getAsString();
    return $this->getClient() . '::' . $this->getEntityType() . '::' . $identifierString;
  }

  public function getEntityType(): string {
    return $this->entityType;
  }

  public function getClient(): string {
    return $this->client;
  }

  public function getFirstUniqueIdentifierAsString(): string {
    return implode(',', $this->getFirstUniqueIdentifier()->getIdentifierValues());
  }

  public function __toString(): string {
    return $this->getFirstEntityKeyAsString();
  }

  private function getFirstUniqueIdentifier(): UniqueKey {
    return reset($this->uniqueIdentifiers);
  }

  public function getFirstEntityKeyAsString(): string {
    return $this->buildEntityKeyString($this->getFirstUniqueIdentifier());
  }

  public function getEntityKeysAsStrings(): array {
    $keys = [];

    foreach ($this->getUniqueIdentifiers() AS $uniqueIdentifier) {
      $keys[] = $this->buildEntityKeyString($uniqueIdentifier);
    }

    return $keys;
  }
}
