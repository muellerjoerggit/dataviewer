<?php

namespace App\Services\Export\GroupExport_Handler;

use App\DaViEntity\EntityInterface;
use App\Services\Export\ExportConfiguration\ExportPropertyGroupConfiguration;
use App\Services\Export\ExportData\ExportGroup;
use App\Services\Export\ExportRow;
use App\Services\Export\GroupExporter\GroupExporterHandlerInterface;
use App\Services\Export\GroupExporter\GroupTypes;
use App\Services\HtmlService;

class HtmlExportHandler extends AbstractGroupExportHandler implements GroupExporterHandlerInterface {

  public function __construct(
    private readonly HtmlService $htmlService,
  ) {}

  public function fillExportGroup(ExportRow $row, ExportGroup $exportGroup, EntityInterface $entity): void {
    $config = $exportGroup->getConfig();

    if(!$config instanceof ExportPropertyGroupConfiguration) {
      return;
    }

    $html = $entity->getPropertyItem($config->getProperty())->getFirstValueAsString();
    $exportGroup->addData($row, [$this->htmlService->htmlToText($html)]);
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
