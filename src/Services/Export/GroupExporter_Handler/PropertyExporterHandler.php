<?php

namespace App\Services\Export\GroupExporter_Handler;

use App\DaViEntity\EntityInterface;
use App\Services\Export\ExportConfiguration\ExportPropertyGroupConfiguration;
use App\Services\Export\ExportData\ExportGroup;
use App\Services\Export\ExportRow;
use App\Services\Export\GroupExporter\GroupExporterHandlerInterface;
use App\Services\Export\GroupExporter\GroupTypes;

class PropertyExporterHandler implements GroupExporterHandlerInterface {

  public function fillExportGroup(ExportRow $row, ExportGroup $exportGroup, EntityInterface $entity): void {
    $config = $exportGroup->getConfig();

    if(!$config instanceof ExportPropertyGroupConfiguration) {
      return;
    }

    $key = sha1($entity->getFirstEntityKeyAsString());
    $data[$key] = $entity->getPropertyItem($config->getProperty())->getValuesAsString();
    $exportGroup->addData($row, $data);
  }

  public function getName(): string {
    return 'DefaultPropertyExporter';
  }

  public function getLabel(): string {
    return 'Standard Export';
  }

  public function getDescription(): string {
    return 'Standard Export für ein Feld';
  }

  public function getRowAsArray(ExportRow $row, string $prefix, ExportGroup $exportGroup): array {
    $data = $exportGroup->getRowData($row);
    $ret = [];

    $suffix = 1;
    if(is_array($data)) {
      foreach($data as $item) {
        $ret[$prefix . '_' . $exportGroup->getKey() . '_' . $suffix] = $item;
        $suffix++;
      }
    } else {
      $ret[$prefix . '_' . $exportGroup->getKey() . '_' . $suffix] = $data;
    }

    return $ret;
  }

  public function getRowAsArraySorted(ExportRow $row, string $prefix, ExportGroup $exportGroup): array {
    $data = $exportGroup->getRowData($row);
    $ret = [];

    $suffix = 1;
    if(is_array($data)) {
      foreach($data as $key => $item) {
        $ret[$key] = array_merge($ret[$key] ?? [], [$prefix . '_' . $exportGroup->getKey() . '_' . $suffix => $item]);
        $suffix++;
      }
    } else {
      $ret[$prefix . '_' . $exportGroup->getKey() . '_' . $suffix] = $data;
    }

    return $ret;
  }

  public function getType(): int {
    return GroupTypes::PROPERTY;
  }

}