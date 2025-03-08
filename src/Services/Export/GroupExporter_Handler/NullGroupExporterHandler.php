<?php

namespace App\Services\Export\GroupExporter_Handler;

use App\DaViEntity\EntityInterface;
use App\Services\Export\ExportData\ExportGroup;
use App\Services\Export\ExportRow;
use App\Services\Export\GroupExporter\GroupExporterHandlerInterface;

class NullGroupExporterHandler implements GroupExporterHandlerInterface {

  public function fillExportGroup(ExportRow $row, ExportGroup $exportGroup, EntityInterface $entity): void {
    $key = sha1($entity->getFirstEntityKeyAsString());
    $exportGroup->addData($row, [$key =>'']);
  }

  public function getName(): string {
    return 'NullGroupExporterHandler';
  }

  public function getLabel(): string {
    return 'Null Exporter';
  }

  public function getDescription(): string {
    return 'Null Exporter';
  }

  public function getRowAsArray(ExportRow $row, ExportGroup $exportGroup): array {
    return [];
  }

  public function getRowAsArraySorted(ExportRow $row, ExportGroup $exportGroup): array {
    return [];
  }

  public function getType(): int {
    return -1;
  }

  public function getHeader(ExportGroup $exportGroup): array {
    return [];
  }

}