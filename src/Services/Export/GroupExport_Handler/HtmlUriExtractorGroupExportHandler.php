<?php

namespace App\Services\Export\GroupExport_Handler;

use App\DaViEntity\EntityInterface;
use App\Services\Export\ExportConfiguration\ExportPropertyGroupConfiguration;
use App\Services\Export\ExportData\ExportGroup;
use App\Services\Export\ExportRow;
use App\Services\Export\GroupExporter\GroupTypes;
use App\Services\HtmlService;
use InvalidArgumentException;
use Symfony\Component\DomCrawler\Crawler;

class HtmlUriExtractorGroupExportHandler extends AbstractGroupExportHandler {

  public function __construct(
    private readonly HtmlService $htmlService,
  ) {}

  public function fillExportGroup(ExportRow $row, ExportGroup $exportGroup, EntityInterface $entity): void {
    $config = $exportGroup->getConfig();

    if(!$config instanceof ExportPropertyGroupConfiguration) {
      return;
    }

    $urls = [];
    $values = $entity->getPropertyItem($config->getProperty())->getValuesAsArray();

    foreach ($values as $html) {
      $urls = array_merge($urls, $this->htmlService->extractUris($html));
    }

    $exportGroup->addData($row, $urls);
  }

  public function getName(): string {
    return 'UriExtractor';
  }

  public function getLabel(): string {
    return 'Uri Extractor';
  }

  public function getDescription(): string {
    return 'Extrahiert die URI aus Link- und Image-Tags im HTML';
  }

  public function getType(): int {
    return GroupTypes::PROPERTY;
  }

}
