<?php

namespace App\Services\Export\GroupExporter_Handler;

use App\DaViEntity\EntityInterface;
use App\Services\Export\ExportConfiguration\ExportPropertyGroupConfiguration;
use App\Services\Export\ExportData\ExportGroup;
use App\Services\Export\ExportRow;
use App\Services\Export\GroupExporter\GroupExporterHandlerInterface;
use App\Services\Export\GroupExporter\GroupTypes;

class PropertyExporterHandler extends AbstractGroupExporterHandler implements GroupExporterHandlerInterface {

  public function fillExportGroup(ExportRow $row, ExportGroup $exportGroup, EntityInterface $entity): void {
    $config = $exportGroup->getConfig();

    if(!$config instanceof ExportPropertyGroupConfiguration) {
      return;
    }

    $key = $this->getEntityKeyHash($entity);
    $data[$key] = $entity->getPropertyItem($config->getProperty())->getValuesAsString();
    $exportGroup->addData($row, $data);
  }

  public function getName(): string {
    return 'DefaultPropertyExporter';
  }

  public function getLabel(): string {
    return 'Standard Export';
  }

  public function getDescription(): string {
    return 'Standard Export für ein Feld';
  }

  public function getType(): int {
    return GroupTypes::PROPERTY;
  }

}