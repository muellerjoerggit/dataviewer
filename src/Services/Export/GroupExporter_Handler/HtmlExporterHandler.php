<?php

namespace App\Services\Export\GroupExporter_Handler;

use App\DaViEntity\EntityInterface;
use App\Services\Export\ExportConfiguration\ExportPropertyGroupConfiguration;
use App\Services\Export\ExportData\ExportGroup;
use App\Services\Export\ExportRow;
use App\Services\Export\GroupExporter\GroupExporterHandlerInterface;
use App\Services\Export\GroupExporter\GroupTypes;
use App\Services\HtmlService;

class HtmlExporterHandler extends AbstractGroupExporterHandler implements GroupExporterHandlerInterface {

  public function __construct(
    private readonly HtmlService $htmlService,
  ) {}

  public function fillExportGroup(ExportRow $row, ExportGroup $exportGroup, EntityInterface $entity): void {
    $config = $exportGroup->getConfig();

    if(!$config instanceof ExportPropertyGroupConfiguration) {
      return;
    }

    $key = $this->getEntityKeyHash($entity);
    $html = $entity->getPropertyItem($config->getProperty())->getFirstValueAsString();
    $data[$key] = $this->htmlService->htmlToText($html);
    $exportGroup->addData($row, $data);
  }

  public function getName(): string {
    return 'HtmlExporter';
  }

  public function getLabel(): string {
    return 'Html entfernen';
  }

  public function getDescription(): string {
    return 'Entfernt HTML-Tags';
  }

  public function getType(): int {
    return GroupTypes::PROPERTY;
  }

}