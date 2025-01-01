<?php

namespace App\Database\TableReference;

use App\DaViEntity\Schema\EntitySchema;
use App\Services\AppNamespaces;

class TableReferenceConfigurationBuilder {

  public function __construct(
    private readonly TableReferenceHandlerLocator $handlerLocator,
  ) {}

  public function processYaml(array $yaml, EntitySchema $schema): void {
    foreach ($yaml as $key => $config) {
      $tableReferenceConfiguration = $this->buildTableReferenceConfiguration($config, $key, $schema);
      if(!$tableReferenceConfiguration) {
        continue;
      }

      $schema->addTableReference($tableReferenceConfiguration, $key);
    }
  }

  public function buildTableReferenceConfiguration(array | string $config, string $key, EntitySchema $schema): TableReferenceConfiguration | null {
    if (is_array($config)) {
      $handler = key($config);
      $config = reset($config);
    } elseif (is_string($config)) {
      $handler = $config;
      $config = [];
    } else {
      return null;
    }

    $handler = AppNamespaces::buildNamespace(AppNamespaces::TABLE_REFERENCE_HANDLER, $handler);

    if(!$this->handlerLocator->has($handler)) {
      return null;
    }

    $name = $this->buildName($key, $schema->getEntityType());

    $tableReferenceConfiguration = TableReferenceConfiguration::create($handler, $name, $schema->getEntityType());
    if(!empty($config)) {
      $tableReferenceConfiguration->setSettings($config);
    }
    return $tableReferenceConfiguration;
  }

  private function buildName(string $key, string $entityType): string {
    return $entityType . '-' . $key;
  }

}