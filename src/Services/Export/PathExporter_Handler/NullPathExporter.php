<?php

namespace App\Services\Export\PathExporter_Handler;

use App\DaViEntity\EntityInterface;
use App\Services\Export\ExportData\PathExport;
use App\Services\Export\ExportRow;
use App\Services\Export\PathExporter\PathExporterHandlerInterface;

class NullPathExporter implements PathExporterHandlerInterface {

  public function processEntityPath(ExportRow $row, PathExport $exportPath, EntityInterface $baseEntity): void {}

  public function getName(): string {
    return 'NullPathExporter';
  }

  public function getLabel(): string {
    return 'Fehler';
  }

  public function getDescription(): string {
    return 'Fehler';
  }

}