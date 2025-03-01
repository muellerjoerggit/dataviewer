<?php

namespace App\Services\Export;

use App\Database\SqlFilter\FilterContainer;
use App\DaViEntity\DaViEntityManager;
use App\DaViEntity\EntityKey;
use App\Services\BackgroundTask\BackgroundTaskManager;
use App\Services\BackgroundTask\BackgroundTaskTracker;
use App\Services\Export\ExportData\ExportData;
use App\Services\Export\GroupExporter\GroupExporterLocator;
use App\Services\Export\PathExporter\PathExporterLocator;
use App\Services\ProgressTracker\TrackerInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;

class CsvExport {

  public function __construct(
    private readonly DaViEntityManager $entityManager,
    private readonly PathExporterLocator $pathExporterLocator,
    private readonly GroupExporterLocator $groupExporterLocator,
  ) {}

  public function export(ExportData $exportData, TrackerInterface | null $tracker = null): string {
    if(!$exportData->isValid()) {
      return '';
    }

    $filterContainer = new FilterContainer($exportData->getClient());
    $filterContainer->setLimit(1000);
    $entityList = $this->entityManager->getEntityList($exportData->getStartEntityClass(), $filterContainer);
    $label = $exportData->getEntityTypeLabel();

    $count = 0;
    $total = $entityList->getTotalCount();
    foreach ($entityList->iterateEntityList() as $entityData) {
      $entityKey = EntityKey::createFromString($entityData['entityKey']);
      $entity = $this->entityManager->getEntity($entityKey);
      $row = new ExportRow('row' . $count);
      $exportData->addRow($row);
      foreach ($exportData->iterateExportPath() as $exportPath) {
        $handler = $this->pathExporterLocator->getPathExporter($exportPath);
        $handler->processEntityPath($row, $exportPath, $entity);
      }
      $count++;

      if($tracker && $tracker->shouldProgressBeRecorded($count)) {

        if($tracker instanceof BackgroundTaskTracker) {
          $tracker->setProgress(json_encode([
            'type' => BackgroundTaskManager::PROGRESS_TYPE_COUNT_ENTITIES,
            'label' => $label,
            'processedEntities' => $count,
            'totalEntities' => $total
          ]));
        }
        if($tracker->isTerminated()) {
          return '';
        }
      }
    }

    return $this->buildCsv($exportData);
  }

  private function buildCsv(ExportData $exportData): string {
    $encoder = new CsvEncoder();
    $data = $this->exportDataToArray($exportData);
    return $encoder->encode($data, 'csv', [
      CsvEncoder::DELIMITER_KEY => ';',
      CsvEncoder::NO_HEADERS_KEY => true,
    ]);
  }

  private function exportDataToArray(ExportData $exportData): array {
    $ret = [];
    $header = [];

    foreach ($exportData->iterateRows() as $row) {
      $rowArray = [];
      foreach ($exportData->iterateExportPath() as $index => $pathData) {
        foreach ($pathData->iterateExportGroups() as $group) {
          $handler = $this->groupExporterLocator->getGroupExporter($group);
          $rowArray = array_merge_recursive($rowArray, $handler->getRowAsArraySorted($row, (string)$index, $group));
        }
      }
      $ret[] = $this->flattenArray($rowArray);
    }

//    return array_merge([$header], $ret);
    return $ret;
  }

  private function flattenArray(array $array): array {
    $ret = [];
    array_walk_recursive($array, function($value) use (&$ret) {
      $ret[] = $value;
    });
    return $ret;
  }

}