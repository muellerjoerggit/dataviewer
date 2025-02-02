<?php

namespace App\Services\Export;

use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use App\DaViEntity\Schema\EntityTypesRegister;
use App\Services\ClientService;
use App\Services\Export\ExportConfiguration\ExportConfiguration;
use App\Services\Export\ExportConfiguration\ExportEntityPathConfiguration;
use App\Services\Export\ExportConfiguration\ExportPropertyConfig;
use DateTime;

class ExportConfigurationBuilder {

  public const string START_ENTITY_PATH = '::start::';

  public const string CONFIG_EXPORT = 'export';
  public const string CONFIG_CLIENT = 'client';
  public const string CONFIG_PATH = 'path';
  public const string CONFIG_TARGET_ENTITY = 'targetEntityType';
  public const string CONFIG_PROPERTIES = 'properties';
  public const string CONFIG_PROPERTY_KEY = 'propertyKey';
  public const string CONFIG_PROPERTY_LABEL = 'label';
  public const string CONFIG_PROPERTY_COUNT = 'count';

  public function __construct(
    private readonly EntityTypeSchemaRegister $schemaRegister,
    private readonly EntityTypesRegister $entityTypesRegister,
    private readonly ClientService $clientService,
  ) {}

  public function build(array $configArray): ExportConfiguration {
    $configuration = new ExportConfiguration($configArray[self::CONFIG_CLIENT]);
    foreach ($configArray[self::CONFIG_EXPORT] as $pathKey => $exportPath) {
      $path = $exportPath[self::CONFIG_PATH][self::CONFIG_PATH] ?? [];
      $entityType = $exportPath[self::CONFIG_PATH][self::CONFIG_TARGET_ENTITY];
      $schema = $this->schemaRegister->getEntityTypeSchema($entityType);

      if($pathKey === self::START_ENTITY_PATH) {
        $entityClass = $this->entityTypesRegister->getEntityClassByEntityType($entityType);
        $configuration
          ->setStartEntityClass($entityClass)
          ->setEntityTypeLabel($schema->getEntityLabel());
      }

      $pathConfig = new ExportEntityPathConfiguration($path);
      $configuration->addEntityPath($pathConfig);

      $properties = $exportPath[self::CONFIG_PROPERTIES] ?? [];
      foreach ($properties as $key => $propertyArray) {
        $property = $propertyArray[self::CONFIG_PROPERTY_KEY];
        $propertyConfig = new ExportPropertyConfig(
          $key,
          $schema->getProperty($property),
        );

        $propertyConfig
          ->setLabel($propertyArray[self::CONFIG_PROPERTY_LABEL] ?? '')
          ->setCount($propertyArray[self::CONFIG_PROPERTY_COUNT] ?? 0);

        $pathConfig->addPropertyConfig($propertyConfig);
      }
    }

    $this->buildFileName($configuration);

    return $configuration;
  }

  private function buildFileName(ExportConfiguration $configuration): void {
    if(!$configuration->isValid()) {
      return;
    }

    $client = $this->clientService->getClientName($configuration->getClient());
    $date = (new DateTime())->format('d.m.Y H:i');
    $label = $this->schemaRegister->getSchemaFromEntityClass($configuration->getStartEntityClass())->getEntityLabel();
    $configuration->setFileName($client . ' Export ' .  $label . ' ' .  $date . '.csv');
  }

}