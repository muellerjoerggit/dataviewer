<?php

namespace App\Services\Export\GroupExport_Handler;

use App\DaViEntity\EntityInterface;
use App\Services\Export\ExportData\ExportGroup;
use App\Services\Export\ExportRow;
use App\Services\Export\GroupExporter\GroupExporterHandlerInterface;
use Generator;

abstract class AbstractGroupExportHandler implements GroupExporterHandlerInterface {

  public function getHeaderColumn(ExportGroup $exportGroup, string $suffix, bool $firstColumn = false): array {
    $label = $exportGroup->getLabel();
    $fullKey = $exportGroup->getFullKey();

    return [$fullKey . '_' . $suffix => $firstColumn ? $label : $label . ' ' . $suffix];
  }

  public function getRowAsArray(ExportRow $row, ExportGroup $exportGroup): array {
    $data = $exportGroup->getRowData($row);
    $ret = [];

    $suffix = 1;
    if(is_array($data)) {
      foreach($data as $item) {
        $ret[$exportGroup->getFullKey() . '_' . $suffix] = $item;
        $suffix++;
      }
    } else {
      $ret[$exportGroup->getFullKey() . '_' . $suffix] = $data;
    }

    return $ret;
  }

}
