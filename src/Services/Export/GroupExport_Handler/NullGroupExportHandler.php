<?php

namespace App\Services\Export\GroupExport_Handler;

use App\DaViEntity\EntityInterface;
use App\Services\Export\ExportData\ExportGroup;
use App\Services\Export\ExportRow;
use App\Services\Export\GroupExporter\GroupExporterHandlerInterface;

class NullGroupExportHandler implements GroupExporterHandlerInterface {

  public function fillExportGroup(ExportRow $row, ExportGroup $exportGroup, EntityInterface $entity): void {
    $exportGroup->addData($row, ['']);
  }

  public function getName(): string {
    return 'NullGroupExportHandler';
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

  public function getType(): int {
    return -1;
  }

  public function getHeaderColumn(ExportGroup $exportGroup, string $suffix, bool $firstColumn = FALSE): array {
    return [];
  }

}
