<?php

namespace App\Services\Export\GroupExporter;

use App\Services\Export\GroupExport_Handler\NullGroupExportHandler;

class GroupExporterRegister {

  private array $groupExporterHandlers = [];
  private array $groupTypes = [];

  public function __construct(
    private readonly GroupExporterLocator $groupExporterLocator,
  ) {
    $this->init();
  }

  private function init(): void {
    foreach ($this->groupExporterLocator->iterateGroupExporters() as $groupExporter) {
      if($groupExporter instanceof NullGroupExportHandler) {
        continue;
      }
      $this->groupExporterHandlers[$groupExporter->getName()] = [
        'name' => $groupExporter->getName(),
        'label' => $groupExporter->getLabel(),
        'description' => $groupExporter->getDescription(),
        'class' => $groupExporter::class,
      ];
      $this->groupTypes[$groupExporter->getType()][] = $groupExporter->getName();
    }
  }

  public function getExporterClass(string $name): string {
    return $this->groupExporterHandlers[$name]['class'] ?? NullGroupExportHandler::class;
  }

  public function getGroupExporterList(int $type = 0): array {
    $ret = [];
    foreach ($this->groupExporterHandlers as $name => $groupExporter) {
      if($type > 0 && !in_array($name, $this->groupTypes[$type])) {
        continue;
      }
      unset($groupExporter['class']);
      $ret[$name] = $groupExporter;
    }
    return $ret;
  }

}
