<?php

namespace App\Services\Export\PathExporter_Handler;

use App\DaViEntity\DaViEntityManager;
use App\DaViEntity\EntityInterface;
use App\Services\Export\ExportData\PathExport;
use App\Services\Export\ExportRow;
use App\Services\Export\GroupExporter\GroupExporterLocator;
use App\Services\Export\PathExporter\PathExporterHandlerInterface;

class CommonPathExporter implements PathExporterHandlerInterface {

  public function __construct(
    private readonly DaViEntityManager $entityManager,
    private readonly GroupExporterLocator $groupExporterLocator,
  ) {}

  public function processEntityPath(ExportRow $row, PathExport $exportPath, EntityInterface $baseEntity): void {
    if (!$exportPath->hasPath()) {
      $entities = [$baseEntity];
    } else {
      $entities = $this->entityManager->getEntitiesFromEntityPath($exportPath->getPath(), $baseEntity);
    }

    foreach ($entities as $entity) {
      $this->processEntity($row, $exportPath, $entity);
    }
  }

  private function processEntity(ExportRow $row, PathExport $exportPath, EntityInterface $entity): void {
    foreach ($exportPath->iterateExportGroups() as $exportGroup) {
      $handler = $this->groupExporterLocator->getGroupExporter($exportGroup);
      $handler->fillExportGroup($row, $exportGroup, $entity);
    }
  }

  public function getName(): string {
    return 'CommonPathExporter';
  }

  public function getLabel(): string {
    return 'CommonPathExporter';
  }

  public function getDescription(): string {
    return 'Standard Exporter, der mehrere Entitäten auf mehrere Spalten aufteilt';
  }

}