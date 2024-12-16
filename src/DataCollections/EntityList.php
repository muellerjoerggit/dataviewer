<?php

namespace App\DataCollections;

use App\Logger\LogItems\LogItemInterface;
use Generator;

class EntityList {

  private array $entityList = [];

  private int $lowerBound;

  private int $upperBound;

  private array $loggingList = [];

  private int $total_count = 0;

  private bool $useBound = FALSE;

  private int $page = 0;

  public function getCount(): int {
    return count($this->entityList);
  }

  public function getTotalCount(): int {
    return $this->total_count ?? 0;
  }

  public function setTotalCount(int $total_count): EntityList {
    $this->total_count = $total_count;
    return $this;
  }

  public function getLowerBound(): int {
    return $this->lowerBound ?? 0;
  }

  public function setLowerBound(int $lowerBound): EntityList {
    $this->lowerBound = $lowerBound;
    return $this;
  }

  public function getUpperBound(): int {
    return $this->upperBound ?? 0;
  }

  public function setUpperBound(int $lowerBound): EntityList {
    $this->upperBound = $lowerBound;
    return $this;
  }

  public function addEntities(array $entities): EntityList {
    if ($this->isValidEntityArray($entities)) {
      return $this->addEntity($entities);
    }

    foreach ($entities as $entity) {
      if (!is_array($entity)) {
        continue;
      }
      $this->addEntity($entity);
    }

    ksort($this->entityList, SORT_NATURAL);

    return $this;
  }

  private function isValidEntityArray(array $entity): bool {
    return array_key_exists('entityLabel', $entity)
      && array_key_exists('entityKey', $entity)
      && array_key_exists('uniqueKey', $entity);
  }

  public function addEntity(array $entity): EntityList {
    if ($this->isValidEntityArray($entity)) {
      $entityKey = $entity['entityKey'];
      $this->entityList[$entityKey] = $entity;

      if ($this->isUseBound()) {
        $this->evaluateBound($entity['uniqueKey']);
      }
    }

    return $this;
  }

  public function isUseBound(): bool {
    return $this->useBound;
  }

  public function setUseBound(bool $useBound): EntityList {
    $this->useBound = $useBound;
    return $this;
  }

  public function evaluateBound(mixed $bound): bool {
    $ret = FALSE;

    if (!is_integer($bound)) {
      return $ret;
    }

    if (!isset($this->lowerBound) || $bound > ($this->upperBound)) {
      $this->upperBound = $bound;
      $ret = TRUE;
    }

    if (!isset($this->lowerBound) || $bound < ($this->lowerBound)) {
      $this->lowerBound = $bound;
      $ret = TRUE;
    }

    return $ret;
  }

  public function getLoggingList(): array {
    return $this->loggingList;
  }

  public function addLogs(array $logItems): EntityList {
    foreach ($logItems as $logItem) {
      if (!($logItem instanceof LogItemInterface)) {
        continue;
      }
      $this->addLog($logItem);
    }
    return $this;
  }

  public function addLog(LogItemInterface $logItem): EntityList {
    $this->loggingList[] = $logItem;
    return $this;
  }

  public function getEntityList(): array {
    return $this->entityList;
  }

  public function iterateEntityList(): Generator {
    foreach ($this->entityList as $entityKey => $entity) {
      yield $entityKey => $entity;
    }
  }

  public function getPage(): int {
    return $this->page;
  }

  public function setPage(int $page): EntityList {
    $this->page = $page;
    return $this;
  }

}
