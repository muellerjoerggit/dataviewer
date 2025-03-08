<?php

namespace App\Services\Export\ExportData;

use App\Services\Export\ExportRow;
use Generator;

class ExportData {

  /** @var ExportRow[] */
  private array $rows;

  /** @var PathExport[] */
  private array $entityPaths;

  private string $startEntityClass;
  private string $entityTypeLabel;
  private string $fileName;

  public function __construct(
    private readonly string $client,
  ) {}

  public function getClient(): string {
    return $this->client;
  }

  public function setStartEntityClass(string $startEntityClass): ExportData {
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

  public function addRow(ExportRow $row): static {
    $this->rows[] = $row;
    return $this;
  }

  public function addEntityPath(PathExport $path): static {
    $this->entityPaths[] = $path;
    $path->setIndex(array_key_last($this->entityPaths));
    return $this;
  }

  /**
   * @return Generator<PathExport>
   */
  public function iterateExportPath(): Generator {
    foreach ($this->entityPaths as $path) {
      yield $path;
    }
  }

  /**
   * @return \Generator<ExportRow>
   */
  public function iterateRows(): Generator {
    foreach ($this->rows as $row) {
      yield $row;
    }
  }

  public function getFileName(): string {
    return $this->fileName ?? 'Export.csv';
  }

  public function setFileName(string $fileName): ExportData {
    $this->fileName = $fileName;
    return $this;
  }

  public function getEntityTypeLabel(): string {
    return $this->entityTypeLabel ?? 'Entitäten';
  }

  public function setEntityTypeLabel(string $entityTypeLabel): ExportData {
    $this->entityTypeLabel = $entityTypeLabel;
    return $this;
  }

}