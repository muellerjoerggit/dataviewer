<?php

namespace App\Services\Export\PathExporter;

use App\Services\Export\PathExport_Handler\NullPathExportHandler;

class PathExporterRegister {

  private array $pathExporterHandlers = [];

  public function __construct(
    private readonly PathExporterLocator $pathExporterLocator,
  ) {
    $this->init();
  }

  private function init(): void {
    foreach ($this->pathExporterLocator->iteratePathExporters() as $pathExporter) {
      if($pathExporter instanceof NullPathExportHandler) {
        continue;
      }
      $this->pathExporterHandlers[$pathExporter->getName()] = [
        'name' => $pathExporter->getName(),
        'label' => $pathExporter->getLabel(),
        'description' => $pathExporter->getDescription(),
        'class' => $pathExporter::class,
      ];
    }
  }

  public function getExporterClass(string $name): string {
    return $this->pathExporterHandlers[$name]['class'] ?? NullPathExportHandler::class;
  }


}
