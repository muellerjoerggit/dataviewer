<?php

namespace App\DaViEntity;

/**
 * TODO: improve if(isset($firstIdentifier[0])) ...
 */
class EntityKey {

  private string $entityType;

  private array $uniqueIdentifiers;

  private string $client;

  private string $parameterPath = '';

  public function __construct(string $client, string $entityType, array $uniqueIdentifiers, string $parameterPath = '') {
    $this->client = $client;
    $this->entityType = $entityType;
    $this->uniqueIdentifiers = $uniqueIdentifiers;
    $this->parameterPath = $parameterPath;
  }

  public static function createNullEntityKey(): EntityKey {
    $entityType = 'NullEntity';
    $uniqueIdentifiers = ['id' => NULL];

    return new static ('', $entityType, $uniqueIdentifiers);
  }

  public static function createFromString(string $entityKeyString, bool $dontReturnNullEntity = FALSE): EntityKey|null {
    $uniqueIdentifier = [];
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

    foreach ($properties as $key => $property) {
      $uniqueIdentifier[$property] = $values[$key];
    }

    if ($error && $dontReturnNullEntity) {
      return NULL;
    } elseif ($error) {
      return self::create('', 'NullEntity', ['id' => NULL]);
    }

    $parameterPath = $keyParts[4] ?? '';
    return self::create($keyParts[0], $keyParts[1], [$uniqueIdentifier], $parameterPath);
  }

  public static function create(string $client, string $entityType, array $uniqueIdentifiers, $parameterPath = ''): EntityKey {
    if (empty($entityType) || empty($uniqueIdentifiers)) {
      $entityType = 'NullEntity';
      $uniqueIdentifiers = [['id' => NULL]];
    }

    return new static ($client, $entityType, $uniqueIdentifiers, $parameterPath);
  }

  public function getEntityKeysAsStrings(): array {
    $keys = [];

    foreach ($this->getUniqueIdentifiers() as $uniqueIdentifier) {
      if (!is_array($uniqueIdentifier)) {
        continue;
      }

      $keys[] = $this->buildEntityKeyString($uniqueIdentifier);
    }

    return $keys;
  }

  public function getUniqueIdentifiers(): array {
    return $this->uniqueIdentifiers;
  }

  public function setUniqueIdentifiers(array $uniqueIdentifiers) {
    $this->uniqueIdentifiers = $uniqueIdentifiers;
  }

  private function buildEntityKeyString(array $uniqueIdentifier): string {
    $keys = $this->buildUniqueIdentifier($uniqueIdentifier);

    $key = $this->getEntityType() . '::' . $keys;

    if (!empty($this->parameterPath)) {
      $key = $key . '::' . $this->parameterPath;
    }

    return $this->getClient() . '::' . $key;
  }

  private function buildUniqueIdentifier(array $uniqueIdentifier, bool $onlyValuesPart = FALSE): string {
    $keys = '';
    $values = '';
    if (count($uniqueIdentifier) > 1) {
      foreach ($uniqueIdentifier as $key => $value) {
        $keys = empty($keys) ? $key : $keys . '+' . $key;
        $values = empty($values) ? $value : $values . '+' . $value;
      }
    } else {
      $keys = key($uniqueIdentifier);
      $values = current($uniqueIdentifier);
    }

    if ($onlyValuesPart) {
      return $values;
    }

    return $keys . '::' . $values;
  }

  public function getEntityType(): string {
    return $this->entityType;
  }

  public function getClient(): string {
    return $this->client;
  }

  public function getFirstUniqueIdentifierAsString(): string {
    $firstIdentifier = $this->getUniqueIdentifiers();

    if (isset($firstIdentifier[0])) {
      $firstIdentifier = $firstIdentifier[0];
    }

    return $this->buildUniqueIdentifier($firstIdentifier, TRUE);
  }

  public function __toString(): string {
    return $this->getFirstEntityKeyAsString();
  }

  public function getFirstEntityKeyAsString(): string {
    $firstIdentifier = $this->getUniqueIdentifiers();
    if (isset($firstIdentifier[0])) {
      $firstIdentifier = $firstIdentifier[0];
    }

    return $this->buildEntityKeyString($firstIdentifier);
  }

  public function getParameterPath(): string {
    return $this->parameterPath ?? '';
  }

  public function isEqual(EntityKey $secondEntityKey): bool {
    if ($this->getClient() != $secondEntityKey->getClient()) {
      return FALSE;
    }

    if ($this->getEntityType() != $secondEntityKey->getEntityType()) {
      return FALSE;
    }

    $serializedThis = $this->serializeUniqueIdentifiers($this->uniqueIdentifiers);
    $serializedSecond = $this->serializeUniqueIdentifiers($secondEntityKey->getUniqueIdentifiers());

    foreach ($serializedThis as $identifier) {
      if (!in_array($identifier, $serializedSecond)) {
        return FALSE;
      }
    }

    return TRUE;
  }

  public function serializeUniqueIdentifiers(array $uniqueIdentifiers): array {
    $ret = [];
    foreach ($uniqueIdentifiers as $uniqueIdentifier) {
      if (is_array($uniqueIdentifier)) {
        ksort($uniqueIdentifier);
        $ret[] = serialize($uniqueIdentifier);
      }
    }

    return $ret;
  }

}
