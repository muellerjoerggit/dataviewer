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
      if (is_array($config)) {
        $handler = key($config);
        $config = reset($config);
      } elseif (is_string($config)) {
        $handler = $config;
      } else {
        continue;
      }

      $handler = AppNamespaces::buildNamespace(AppNamespaces::TABLE_REFERENCE_HANDLER, $handler);

      if(!$this->handlerLocator->has($handler)) {
        continue;
      }

      $name = $this->buildName($key, $schema->getEntityType());

      $tableReferenceConfiguration = TableReferenceConfiguration::create($handler, $name);
      $tableReferenceConfiguration->setSettings($config);
      $schema->addTableReference($tableReferenceConfiguration, $key);
    }
  }

  private function buildName(string $key, string $entityType): string {
    return $entityType . '-' . $key;
  }

}