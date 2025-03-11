<?php

namespace App\Services\Export\PathExport_Handler;

use App\DaViEntity\EntityInterface;
use App\Services\Export\ExportData\PathExport;
use App\Services\Export\ExportRow;
use App\Services\Export\PathExporter\PathExportHandlerInterface;

class NullPathExportHandler implements PathExportHandlerInterface {

  public function processEntityPath(ExportRow $row, PathExport $exportPath, EntityInterface $baseEntity): void {}

  public function getName(): string {
    return 'NullPathExportHandler';
  }

  public function getLabel(): string {
    return 'Fehler';
  }

  public function getDescription(): string {
    return 'Fehler';
  }

}
