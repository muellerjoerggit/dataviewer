<?php

namespace App\Services\Export;

use App\Database\SqlFilter\FilterContainer;
use App\DaViEntity\DaViEntityManager;
use App\DaViEntity\EntityInterface;
use App\DaViEntity\EntityKey;
use App\Services\Export\ExportConfiguration\ExportConfiguration;
use App\Services\Export\ExportConfiguration\ExportEntityPathConfiguration;
use App\Services\Export\ExportData\ExportData;
use App\Services\Export\ExportData\ExportDataEntityPath;
use App\Services\Export\ExportData\ExportDataGroup;
use App\Services\Export\ExportData\ExportDataRow;
use App\Services\ProgressTracker\TrackerInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;

class CsvExport {

  public function __construct(
    private readonly DaViEntityManager $entityManager,
  ) {}

  public function export(ExportConfiguration $exportConfig, ?TrackerInterface $progress = null): string {
    if(!$exportConfig->isValid()) {
      return '';
    }

    $filterContainer = new FilterContainer($exportConfig->getClient());
    $filterContainer->setLimit(1000);
    $entityList = $this->entityManager->getEntityList($exportConfig->getStartEntityClass(), $filterContainer);
    $label = $exportConfig->getEntityTypeLabel();

    $exportData = new ExportData();

    $count = 0;
    $total = $entityList->getTotalCount();
    foreach ($entityList->iterateEntityList() as $entityData) {
      $entityKey = EntityKey::createFromString($entityData['entityKey']);
      $entity = $this->entityManager->getEntity($entityKey);
      $row = new ExportDataRow();
      foreach ($exportConfig->iteratePathConfiguration() as $pathConfigUser) {
        $this->processEntityPath($row, $pathConfigUser, $entity);
      }
      $exportData->addRow($row);
      $count++;

      if($progress && $count % 50 === 0) {
        $progress->setProgress("$count / $total $label exportiert");
        if($progress->isTerminated()) {
          return '';
        }
      }
    }

    return $this->buildCsv($exportData);
  }

  private function processEntityPath(ExportDataRow $row, ExportEntityPathConfiguration $pathConfig, EntityInterface $baseEntity): void {
    if(!$pathConfig->hasPath()) {
      $entities = [$baseEntity];
    } else {
      $entities = $this->entityManager->getEntitiesFromEntityPath($pathConfig->getPath(), $baseEntity);
    }

    foreach ($entities as $entity) {
      $pathData = new ExportDataEntityPath();
      $row->addEntityPathData($pathData);
      $this->processEntity($pathData, $pathConfig, $entity);
    }
  }

  private function processEntity(ExportDataEntityPath $pathData, ExportEntityPathConfiguration $pathConfig, EntityInterface $entity): void {
    $this->exportProperties($pathData, $pathConfig, $entity);
  }

  private function exportProperties(ExportDataEntityPath $pathData, ExportEntityPathConfiguration $pathConfiguration, EntityInterface $entity): void {
    foreach ($pathConfiguration->iteratePropertyConfigs() as $propertyConfig) {
      $values = $entity->getPropertyRawValues($propertyConfig->getProperty());
      $group = new ExportDataGroup($propertyConfig->getKey(), $propertyConfig->getLabel());
      $group->addData($values);
      $pathData->addEntityGroup($group);
    }
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
      foreach ($row->iterateEntityPathData() as $index => $pathData) {
        foreach ($pathData->iterateEntityGroups() as $group) {
          $key = $group->getKey() . $index;
          if(!isset($header[$key])) {
            $label = $group->getLabel();
            $header[$key] = empty($index) ? $label : "$label $index";
          }
          $rowArray[$key] = implode(', ', $group->getData());
        }
      }
      $ret[] = $rowArray;
    }

    return array_merge([$header], $ret);
  }

}