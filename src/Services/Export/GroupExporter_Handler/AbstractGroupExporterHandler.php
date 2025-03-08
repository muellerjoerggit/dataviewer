<?php

namespace App\Services\Export\GroupExporter_Handler;

use App\DaViEntity\EntityInterface;
use App\Services\Export\ExportData\ExportGroup;
use App\Services\Export\ExportRow;
use App\Services\Export\GroupExporter\GroupExporterHandlerInterface;

abstract class AbstractGroupExporterHandler implements GroupExporterHandlerInterface {

  public function getHeader(ExportGroup $exportGroup): array {
    $label = $exportGroup->getLabel();
    $fullKey = $exportGroup->getFullKey();
    $countColumns = $exportGroup->getEntriesCount();

    $ret = [];

    for($i = 1; $i <= $countColumns; $i++) {
      $ret[$fullKey . '_' . $i] = $i === 1 ? $label : $label . ' ' . $i;
    }

    return $ret;
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

  public function getRowAsArraySorted(ExportRow $row, ExportGroup $exportGroup): array {
    if(!$exportGroup->isValid()) {
      return [];
    }

    $data = $exportGroup->getRowData($row);
    $ret = [];

    $suffix = 1;
    if(is_array($data)) {
      foreach($data as $key => $item) {
        $ret[$key] = array_merge($ret[$key] ?? [], [$exportGroup->getFullKey() . '_' . $suffix => $item]);
        $suffix++;
      }
    } else {
      $ret[$exportGroup->getFullKey() . '_' . $suffix] = $data;
    }

    return $ret;
  }

  protected function getEntityKeyHash(EntityInterface $entity): string {
    return sha1($entity->getFirstEntityKeyAsString());
  }

}