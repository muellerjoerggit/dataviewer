<?php

namespace App\Services\Export\ExportData;

use Generator;

class ExportData {

  private array $rows;

  public function addRow(ExportDataRow $row): static {
    $this->rows[] = $row;
    return $this;
  }

  /**
   * @return \Generator<ExportDataRow[]>
   */
  public function iterateRows(): Generator {
    foreach ($this->rows as $row) {
      yield $row;
    }
  }

}