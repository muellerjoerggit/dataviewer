<?php

namespace App\DaViEntity\EntityTypes\NullEntity;

use App\DaViEntity\Attribute\EntityType;
use App\DaViEntity\EntityInterface;
use App\DaViEntity\EntityKey;
use App\DaViEntity\Schema\EntitySchema;
use App\Item\ItemInterface;
use App\Item\Property\PropertyItemInterface;
use App\Logger\LogItems\LogItemInterface;

#[EntityType(name: 'NullEntity')]
class NullEntity implements EntityInterface {

  public const string ENTITY_TYPE = 'NullEntity';

  public function __construct(
    protected readonly EntitySchema $schema,
    protected readonly string $client
  ) {}

  public function getEntityType(): string {
    return self::ENTITY_TYPE;
  }

  public function isMissingEntity(): bool {
    return true;
  }

  public function setMissingEntity(bool $missingEntity): EntityInterface {
    return $this;
  }

  public function getClient(): string {
    return '';
  }

  public function getSchema(): EntitySchema {
    return $this->schema;
  }

  public function countRedLogs(): int {
    return 1;
  }

  public function countYellowLogs(): int {
    return 0;
  }
  public function countLogs(array $logLevels): int {
    return 1;
  }

  public function getEntityTypeLabel(): string {
    return 'NullEntity';
  }

  public function getEntityKeyAsObj(): EntityKey {
    return new EntityKey(
      $this->client,
      self::ENTITY_TYPE,
      [['id' => $this->getPropertyItem('id')->getFirstValue()]]
    );
  }

  public function getFirstEntityKeyAsString(): string {
    return '';
  }

  public function addLogs(LogItemInterface|array $logItems): EntityInterface {
    return $this;
  }

  public function getAllLogs(): array {
    return [];
  }

  public function getAllLogsByLogLevels(array|string $logLevels = [], bool $categorized = TRUE): array {
    return [];
  }

  public function getAllLogsByLogType(array|string $logTypes = []): array {
    return [];
  }

  public function hasCriticalLogs(): bool {
    return true;
  }

  public function iteratePropertyItems(): \Generator {
    yield $this->getPropertyItem('id');
  }

  public function getPropertyRawValues(string $property): mixed {
    return null;
  }

  public function getPropertyValues(string $property): mixed {
    return '';
  }

  public function getPropertyValueAsString(string $property): string {
    return '';
  }

  public function getPropertyItem(string $property): PropertyItemInterface {
    return $this->getPropertyItem('id');
  }

  public function hasPropertyItem(string $property): bool {
    return false;
  }

  public function setPropertyItem(string $property, ItemInterface $item): EntityInterface {
    return $this;
  }

  public function getMultiplePropertyItems(array $properties): array {
    return [];
  }

}