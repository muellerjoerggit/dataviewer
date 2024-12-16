<?php

namespace App\DaViEntity;

use App\DaViEntity\Schema\EntitySchema;
use App\Item\ItemInterface;
use App\Item\Property\PropertyItemInterface;
use App\Logger\LogItems\LogItemInterface;
use Generator;

/**
 * Defines a common interface for all entity objects.
 */
interface EntityInterface {

  public const string LOG_INDEX_TYPE = 'logType';

  public const string LOG_INDEX_LEVEL = 'logLevel';

  public function getEntityType(): string;

  public function getEntityTypeLabel(): string;

  public function getSchema(): EntitySchema;

  public function getClient(): string;

  public function isMissingEntity(): bool;

  public function setMissingEntity(bool $missingEntity): EntityInterface;

  public function getEntityKeyAsObj(): EntityKey;

  public function getFirstEntityKeyAsString(): string;

  public function addLogs(LogItemInterface|array $logItems): EntityInterface;

  public function getAllLogs(): array;

  public function getAllLogsByLogLevels(array|string $logLevels = [], bool $categorized = TRUE): array;

  public function getAllLogsByLogType(array|string $logTypes = []): array;

  public function hasCriticalLogs(): bool;

  public function countLogs(array $logLevels): int;

  public function countRedLogs(): int;

  public function countYellowLogs(): int;

  /**
   * @return \Generator<PropertyItemInterface>
   */
  public function iteratePropertyItems(): Generator;

  public function getPropertyItem(string $property): PropertyItemInterface;

  public function hasPropertyItem(string $property): bool;

  public function getMultiplePropertyItems(array $properties): array;

  public function setPropertyItem(string $property, ItemInterface $item): EntityInterface;

  public function getPropertyRawValues(string $property): mixed;

  public function getPropertyValues(string $property): mixed;

  public function getPropertyValueAsString(string $property): string;

}
