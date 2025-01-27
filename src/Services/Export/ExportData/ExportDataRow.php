<?php

namespace App\Services\Export\ExportData;

use Generator;

class ExportDataRow {

  private array $entityPathData = [];

  public function addEntityPathData(ExportDataEntityPath $entityPathData): static {
    $this->entityPathData[] = $entityPathData;
    return $this;
  }

  /**
   * @return \Generator<ExportDataEntityPath[]>
   */
  public function iterateEntityPathData(): Generator {
    foreach ($this->entityPathData as $index => $entityPathData) {
      yield $index => $entityPathData;
    }
  }

}