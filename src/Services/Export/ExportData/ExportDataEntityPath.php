<?php

namespace App\Services\Export\ExportData;

use Generator;

class ExportDataEntityPath {

  /**
   * @var \App\Services\Export\ExportData\ExportDataGroup[]
   */
  private array $entityPathData = [];

  public function addEntityGroup(ExportDataGroup $group): static {
    $this->entityPathData[] = $group;
    return $this;
  }

  /**
   * @return Generator<\App\Services\Export\ExportData\ExportDataGroup[]>
   */
  public function iterateEntityGroups(): Generator {
    foreach ($this->entityPathData as $group) {
      yield $group;
    }
  }

}