<?php

namespace App\Services\Export\ExportData;

use App\Services\Export\ExportConfiguration\ExportPathConfigurationInterface;
use Generator;

class PathExport {

  /** @var ExportGroup[] */
  private array $exportGroups = [];
  private int $index;

  public function __construct(
    private readonly ExportPathConfigurationInterface $pathConfig,
  ) {}

  public function getPath(): array {
    return $this->pathConfig->getPath();
  }

  public function hasPath(): bool {
    return !empty($this->pathConfig->hasPath());
  }

  public function addExportGroup(ExportGroup $group): static {
    $this->exportGroups[] = $group;
    $group->setPathIndex($this->index);
    return $this;
  }

  /**
   * @return Generator<ExportGroup>
   */
  public function iterateExportGroups(): Generator {
    foreach ($this->exportGroups as $group) {
      yield $group;
    }
  }

  public function getPathConfig(): ExportPathConfigurationInterface {
    return $this->pathConfig;
  }

  public function getPathExporterClass(): string {
    return $this->pathConfig->getPathExporterClass();
  }

  public function setIndex(int $index): void {
    $this->index = $index;
  }

}