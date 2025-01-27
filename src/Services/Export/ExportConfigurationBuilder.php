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

  public function __construct(
    private readonly EntityTypeSchemaRegister $schemaRegister,
    private readonly EntityTypesRegister $entityTypesRegister,
    private readonly ClientService $clientService,
  ) {}

  public function build(array $configArray): ExportConfiguration {
    $configuration = new ExportConfiguration($configArray['client']);
    foreach ($configArray['export'] as $pathKey => $exportPath) {
      $path = $exportPath['path']['path'] ?? [];
      $entityType = $exportPath['path']['targetEntityType'];
      $schema = $this->schemaRegister->getEntityTypeSchema($entityType);

      if($pathKey === self::START_ENTITY_PATH) {
        $entityClass = $this->entityTypesRegister->getEntityClassByEntityType($entityType);
        $configuration
          ->setStartEntityClass($entityClass)
          ->setEntityTypeLabel($schema->getEntityLabel());
      }

      $pathConfig = new ExportEntityPathConfiguration($path);
      $configuration->addEntityPath($pathConfig);

      $properties = $exportPath['properties'] ?? [];
      foreach ($properties as $key => $propertyArray) {
        $property = $propertyArray['propertyKey'];
        $propertyConfig = new ExportPropertyConfig(
          $key,
          $schema->getProperty($property),
        );

        $propertyConfig
          ->setLabel($propertyArray['label'] ?? '')
          ->setCount($propertyArray['count'] ?? 0);

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