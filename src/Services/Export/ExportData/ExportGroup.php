<?php

namespace App\Services\Export\ExportData;

use App\Services\Export\ExportConfiguration\ExportGroupConfigurationInterface;
use App\Services\Export\ExportRow;

class ExportGroup {

  private array $data = [];
  private int $pathIndex;
  private int $entriesCount = 0;

  public function __construct(
    private readonly ExportGroupConfigurationInterface $config,
  ) {}

  public function addData(ExportRow $row, array $data): static {
    $rowKey = $row->getKey();
    $this->data[$rowKey] = array_merge($this->data[$rowKey] ?? [], $data);

    $this->setEntriesCount(count($this->data[$rowKey]));
    return $this;
  }

  public function getData(): array {
    return $this->data;
  }

  public function getRowData(ExportRow $row, mixed $default = ''): mixed {
    return $this->data[$row->getKey()] ?? $default;
  }

  public function getLabel(): string {
    return $this->config->getLabel();
  }

  public function getKey(): string {
    return $this->config->getKey();
  }

  public function getFullKey(): string {
    return $this->pathIndex . '_' . $this->config->getKey();
  }

  public function getExporterClass(): string {
    return $this->config->getExporterClass();
  }

  public function getConfig(): ExportGroupConfigurationInterface {
    return $this->config;
  }

  public function setPathIndex(int $pathIndex): void {
    $this->pathIndex = $pathIndex;
  }

  public function isValid(): bool {
    return isset($this->pathIndex);
  }

  private function setEntriesCount(int $count): void {
    if($count > $this->entriesCount) {
      $this->entriesCount = $count;
    }
  }

  public function getEntriesCount(): int {
    return $this->entriesCount;
  }

}