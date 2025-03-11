<?php

namespace App\Services\Export\GroupExport_Handler;

use App\DaViEntity\EntityInterface;
use App\Services\Export\ExportData\ExportGroup;
use App\Services\Export\ExportRow;
use App\Services\Export\GroupExport_Handler\AbstractGroupExportHandler;

class JsonSplitterGroupExportHandler extends AbstractGroupExportHandler {

  public function fillExportGroup(ExportRow $row, ExportGroup $exportGroup, EntityInterface $entity): void {
    // TODO: Implement fillExportGroup() method.
  }

  public function getName(): string {
    // TODO: Implement getName() method.
  }

  public function getLabel(): string {
    // TODO: Implement getLabel() method.
  }

  public function getDescription(): string {
    // TODO: Implement getDescription() method.
  }

  public function getType(): int {
    // TODO: Implement getType() method.
  }

}
