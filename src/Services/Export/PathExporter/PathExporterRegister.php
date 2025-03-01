<?php

namespace App\Services\Export\PathExporter;

use App\Services\Export\PathExporter_Handler\NullPathExporter;

class PathExporterRegister {

  private array $pathExporterHandlers = [];

  public function __construct(
    private readonly PathExporterLocator $pathExporterLocator,
  ) {
    $this->init();
  }

  private function init(): void {
    foreach ($this->pathExporterLocator->iteratePathExporters() as $pathExporter) {
      if($pathExporter instanceof NullPathExporter) {
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
    return $this->pathExporterHandlers[$name]['class'] ?? NullPathExporter::class;
  }


}