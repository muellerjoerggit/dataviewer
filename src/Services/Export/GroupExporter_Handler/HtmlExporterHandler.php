<?php

namespace App\Services\Export\GroupExporter_Handler;

use App\DaViEntity\EntityInterface;
use App\Services\Export\ExportConfiguration\ExportPropertyGroupConfiguration;
use App\Services\Export\ExportData\ExportGroup;
use App\Services\Export\ExportRow;
use App\Services\Export\GroupExporter\GroupExporterHandlerInterface;
use App\Services\Export\GroupExporter\GroupTypes;
use App\Services\HtmlService;

class HtmlExporterHandler implements GroupExporterHandlerInterface {

  public function __construct(
    private readonly HtmlService $htmlService,
  ) {}

  public function fillExportGroup(ExportRow $row, ExportGroup $exportGroup, EntityInterface $entity): void {
    $config = $exportGroup->getConfig();

    if(!$config instanceof ExportPropertyGroupConfiguration) {
      return;
    }

    $key = sha1($entity->getFirstEntityKeyAsString());
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

  public function getRowAsArray(ExportRow $row, string $prefix, ExportGroup $exportGroup): array {
    return [$prefix . $exportGroup->getKey() => $exportGroup->getRowData($row)];
  }

  public function getRowAsArraySorted(ExportRow $row, string $prefix, ExportGroup $exportGroup): array {
    $data = $exportGroup->getRowData($row);
    $ret = [];

    $suffix = 1;
    if(is_array($data)) {
      foreach($data as $key => $item) {
        $ret[$key] = array_merge($ret[$key] ?? [], [$prefix . '_' . $exportGroup->getKey() . '_' . $suffix => $item]);
        $suffix++;
      }
    } else {
      $ret[$prefix . '_' . $exportGroup->getKey() . '_' . $suffix] = $data;
    }

    return $ret;
  }

  public function getType(): int {
    return GroupTypes::PROPERTY;
  }

}