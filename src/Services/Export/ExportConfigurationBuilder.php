<?php

namespace App\Services\Export;

use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use App\DaViEntity\Schema\EntityTypesRegister;
use App\Services\ClientService;
use App\Services\Export\ExportConfiguration\ExportPathConfiguration;
use App\Services\Export\ExportConfiguration\ExportPropertyGroupConfiguration;
use App\Services\Export\ExportData\ExportData;
use App\Services\Export\ExportData\ExportGroup;
use App\Services\Export\ExportData\PathExport;
use App\Services\Export\GroupExporter\GroupExporterRegister;
use App\Services\Export\GroupExporter\GroupTypes;
use App\Services\Export\PathExporter\PathExporterRegister;
use DateTime;

class ExportConfigurationBuilder {

  public const string START_ENTITY_PATH = '::start::';

  public const string CONFIG_EXPORT = 'export';
  public const string CONFIG_CLIENT = 'client';
  public const string CONFIG_PATH = 'path';
  public const string CONFIG_PATH_EXPORTER_NAME = 'pathExporter';
  public const string CONFIG_TARGET_ENTITY = 'targetEntityType';
  public const string CONFIG_GROUPS = 'groups';
  public const string CONFIG_GROUP_TYPE = 'type';
  public const string CONFIG_GROUP_EXPORTER = 'groupExporter';
  public const string CONFIG_GROUP_KEY = 'groupKey';
  public const string CONFIG_PROPERTY = 'property';
  public const string CONFIG_PROPERTY_LABEL = 'label';
  public const string CONFIG_PROPERTY_COUNT = 'count';

  public function __construct(
    private readonly EntityTypeSchemaRegister $schemaRegister,
    private readonly EntityTypesRegister $entityTypesRegister,
    private readonly ClientService $clientService,
    private readonly PathExporterRegister $exportPathHandlerRegister,
    private readonly GroupExporterRegister $groupExporterRegister,
  ) {}

  public function build(array $configArray): ExportData {
    $exportData = new ExportData($configArray[self::CONFIG_CLIENT]);
    foreach ($configArray[self::CONFIG_EXPORT] as $pathKey => $exportPath) {
      $path = $exportPath[self::CONFIG_PATH][self::CONFIG_PATH] ?? [];
      $entityType = $exportPath[self::CONFIG_PATH][self::CONFIG_TARGET_ENTITY];
      $schema = $this->schemaRegister->getEntityTypeSchema($entityType);

      if($pathKey === self::START_ENTITY_PATH) {
        $entityClass = $this->entityTypesRegister->getEntityClassByEntityType($entityType);
        $exportData
          ->setStartEntityClass($entityClass)
          ->setEntityTypeLabel($schema->getEntityLabel());
      }

      $class = $this->exportPathHandlerRegister->getExporterClass($exportPath[self::CONFIG_PATH_EXPORTER_NAME] ?? 'CommonPathExporter');
      $pathConfig = new ExportPathConfiguration($path, $class);
      $pathExport = new PathExport($pathConfig);
      $exportData->addEntityPath($pathExport);

      $groups = $exportPath[self::CONFIG_GROUPS] ?? [];
      foreach ($groups as $key => $groupArray) {
        switch ($groupArray[self::CONFIG_GROUP_TYPE]) {
          case GroupTypes::PROPERTY:
            $class = $this->groupExporterRegister->getExporterClass($groupArray[self::CONFIG_GROUP_EXPORTER]);
            $property = $groupArray[self::CONFIG_PROPERTY];
            $config = new ExportPropertyGroupConfiguration($key, $class, $schema->getProperty($property),);
            $config->setLabel($groupArray[self::CONFIG_PROPERTY_LABEL] ?? '');
        }

        if(isset($config)) {
          $exportGroup = new ExportGroup($config);
          $pathExport->addExportGroup($exportGroup);
        }
      }
    }

    $this->buildFileName($exportData);

    return $exportData;
  }

  private function buildFileName(ExportData $exportData): void {
    if(!$exportData->isValid()) {
      return;
    }

    $client = $this->clientService->getClientName($exportData->getClient());
    $date = (new DateTime())->format('d.m.Y H:i');
    $label = $this->schemaRegister->getSchemaFromEntityClass($exportData->getStartEntityClass())->getEntityLabel();
    $exportData->setFileName($client . ' Export ' .  $label . ' ' .  $date . '.csv');
  }

}