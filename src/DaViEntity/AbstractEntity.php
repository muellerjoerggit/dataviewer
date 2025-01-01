<?php

namespace App\DaViEntity;

use App\DaViEntity\Schema\EntitySchema;
use App\Logger\LogItems\LogItemInterface;
use Generator;

abstract class AbstractEntity implements EntityInterface {

  protected bool $missingEntity = FALSE;

  protected array $logIndex = [
    EntityInterface::LOG_INDEX_TYPE => [],
    EntityInterface::LOG_INDEX_LEVEL => [],
  ];

  protected array $logItems = [];

  public function __construct(
    protected readonly EntitySchema $schema,
    protected readonly string $client
  ) {}

  public function getEntityTypeLabel(): string {
    return $this->schema->getEntityLabel();
  }

  public function isMissingEntity(): bool {
    return $this->missingEntity;
  }

  public function setMissingEntity(bool $missingEntity): EntityInterface {
    $this->missingEntity = $missingEntity;
    return $this;
  }

  public function getSchema(): EntitySchema {
    return $this->schema;
  }

  public function getFirstEntityKeyAsString(): string {
    return $this->getEntityKeyAsObj()->getFirstEntityKeyAsString();
  }

  public function getEntityKeyAsObj(): EntityKey {
    return EntityKey::create($this->getClient(), $this->getEntityType(), $this->buildUniqueIdentifiers());
  }

  public function getClient(): string {
    return $this->client;
  }

  public function getEntityType(): string {
    return $this->schema->getEntityType();
  }

  protected function buildUniqueIdentifiers(): array {
    $ret = [];
    $uniqueProperties = $this->schema->getUniqueProperties();
    foreach ($uniqueProperties as $properties) {
      $temp = new UniqueIdentifier();
      foreach ($properties as $property) {
        $propertyItem = $this->getPropertyItem($property);
        $propertyValue = $propertyItem->getFirstValue();
        $temp->addIdentifier($property, $propertyValue);
      }
      $ret[] = $temp;
    }

    return $ret;
  }

  public function addLogs(LogItemInterface|array $logItems): EntityInterface {
    if (is_array($logItems)) {
      foreach ($logItems as $logItem) {
        if (!($logItem instanceof LogItemInterface)) {
          continue;
        }

        $this->addLog($logItem);
      }
    } else {
      $this->addLog($logItems);
    }
    return $this;
  }

  protected function addLog(LogItemInterface $logItem): EntityInterface {
    $logLevel = $logItem->getLevel();
    $logType = $logItem->getType();
    $logUuid = $logItem->getUuidAsString();

    $this->logItems[$logUuid] = $logItem;

    $this->logIndex[EntityInterface::LOG_INDEX_TYPE][$logType][$logUuid] = $logItem;
    $this->logIndex[EntityInterface::LOG_INDEX_LEVEL][$logLevel][$logUuid] = $logItem;

    return $this;
  }

  public function getAllLogs(): array {
    return $this->logItems;
  }

  public function getAllLogsByLogLevels(array|string $logLevels = [], bool $categorized = TRUE): array {
    if (empty($logLevels)) {
      $logLevels = array_keys($this->logIndex[EntityInterface::LOG_INDEX_LEVEL]);
    } elseif (is_string($logLevels)) {
      $logLevels = [$logLevels];
    }

    $ret = [];

    foreach ($logLevels as $logLevel) {
      if ($categorized) {
        $ret[$logLevel] = $this->logIndex[EntityInterface::LOG_INDEX_LEVEL][$logLevel] ?? [];
      } else {
        $ret = array_merge($ret, $this->logIndex[EntityInterface::LOG_INDEX_LEVEL][$logLevel] ?? []);
      }
    }

    return $ret;
  }

  public function getAllLogsByLogType(array|string $logTypes = []): array {
    if (empty($logTypes)) {
      $logTypes = array_keys($this->logIndex[self::LOG_INDEX_TYPE]);
    } elseif (is_string($logTypes)) {
      $logTypes = [$logTypes];
    }

    $ret = [];

    foreach ($logTypes as $logType) {
      $ret = array_merge($ret, $this->logIndex[self::LOG_INDEX_TYPE][$logType] ?? []);
    }

    return $ret;
  }

  public function hasCriticalLogs(): bool {
    return (
      isset($this->logIndex[EntityInterface::LOG_INDEX_LEVEL][LogItemInterface::LOG_LEVEL_CRITICAL])
      && count($this->logIndex[EntityInterface::LOG_INDEX_LEVEL][LogItemInterface::LOG_LEVEL_CRITICAL]) > 0
    );
  }

  public function countRedLogs(): int {
    return $this->countLogs(LogItemInterface::RED_LOG_LEVELS);
  }

  public function countLogs(array $logLevels): int {
    $ret = 0;
    foreach ($logLevels as $level) {
      $ret = $ret + count($this->logIndex[EntityInterface::LOG_INDEX_LEVEL][$level] ?? []);
    }

    return $ret;
  }

  public function countYellowLogs(): int {
    return $this->countLogs(LogItemInterface::YELLOW_LOG_LEVELS);
  }

  public function iteratePropertyItems(): Generator {
    foreach ($this->schema->iterateProperties() as $property => $config) {
      yield $property => $this->getPropertyItem($property);
    }
  }

}
