<?php

namespace App\Services\Export\ExportConfiguration;

use Generator;

class ExportConfiguration {

  /**
   * @var \App\Services\Export\ExportConfiguration\ExportEntityPathConfiguration[]
   */
  private array $entityPath = [];
  private string $startEntityClass;
  private string $entityTypeLabel;
  private string $fileName;

  public function __construct(
    private readonly string $client,
  ) {}

  public function addEntityPath(ExportEntityPathConfiguration $pathConfig): static {
    $this->entityPath[] = $pathConfig;
    return $this;
  }

  /**
   * @return Generator<\App\Services\Export\ExportConfiguration\ExportEntityPathConfiguration[]>
   */
  public function iteratePathConfiguration(): Generator {
    foreach ($this->entityPath as $pathConfig) {
      yield $pathConfig;
    }
  }

  public function getClient(): string {
    return $this->client;
  }

  public function setStartEntityClass(string $startEntityClass): ExportConfiguration {
    $this->startEntityClass = $startEntityClass;
    return $this;
  }

  public function getStartEntityClass(): string {
    return $this->startEntityClass;
  }

  public function isValid(): bool {
    if(empty($this->startEntityClass)) {
      return false;
    }

    return true;
  }

  public function getEntityPath(): array {
    return $this->entityPath;
  }

  public function setEntityPath(array $entityPath): ExportConfiguration {
    $this->entityPath = $entityPath;
    return $this;
  }

  public function getFileName(): string {
    return $this->fileName ?? 'Export.csv';
  }

  public function setFileName(string $fileName): ExportConfiguration {
    $this->fileName = $fileName;
    return $this;
  }

  public function getEntityTypeLabel(): string {
    return $this->entityTypeLabel ?? 'Entitäten';
  }

  public function setEntityTypeLabel(string $entityTypeLabel): ExportConfiguration {
    $this->entityTypeLabel = $entityTypeLabel;
    return $this;
  }





}