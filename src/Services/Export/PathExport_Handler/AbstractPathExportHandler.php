<?php

namespace App\Services\Export\PathExport_Handler;

use App\Services\Export\ExportData\PathExport;
use App\Services\Export\GroupExporter\GroupExporterLocator;

abstract class AbstractPathExportHandler {

  public function __construct(
    protected readonly GroupExporterLocator $groupExporterLocator,
  ) {}

  public function getHeader(PathExport $pathExport): array {
    $header = [];
    $max = $this->getMaxGroupEntries($pathExport);

    for($i = 1; $i <= $max; $i++) {
      foreach ($pathExport->iterateExportGroups() as $group) {
        $handler = $this->groupExporterLocator->getGroupExporter($group);
        $header = array_merge($header, $handler->getHeaderColumn($group, (string)$i, $i === 1));
      }
    }

    return $header;
  }

  protected function getMaxGroupEntries(PathExport $pathExport): int {
    $max = 0;

    foreach ($pathExport->iterateExportGroups() as $group) {
      $max = max($max, $group->getEntriesCount());
    }

    return $max;
  }

}
