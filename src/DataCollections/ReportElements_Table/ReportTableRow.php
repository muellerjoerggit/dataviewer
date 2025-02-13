<?php

namespace App\DataCollections\ReportElements_Table;

class ReportTableRow {

  private array $cells = [];

  public static function create(ReportTableCell ...$cells): ReportTableRow {
    $row = new static();
    foreach ($cells as $cell) {
      $row->addCell($cell);
    }

    return $row;
  }

  public function addCell(ReportTableCell $cell): ReportTableRow {
    $this->cells[$cell->getColumnKey()] = $cell;
    return $this;
  }

  public function toArray(): array {
    return array_map(function($cell) {
      return $cell->toArray();
    }, $this->cells);
  }


}