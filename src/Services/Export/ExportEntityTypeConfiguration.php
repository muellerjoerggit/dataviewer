<?php

namespace App\Services\Export;

use App\DaViEntity\Schema\EntitySchema;
use App\DaViEntity\Schema\EntityTypeSchemaRegister;
use App\DaViEntity\Schema\EntityTypesRegister;
use App\Item\ItemConfigurationInterface;
use App\Item\ItemHandler_EntityReference\EntityReferenceItemHandlerLocator;
use App\Item\ItemHandler_EntityReference\SimpleEntityReferenceJoinInterface;
use App\Services\Export\GroupExporter\GroupExporterRegister;
use App\Services\Export\GroupExporter\GroupTypes;

class ExportEntityTypeConfiguration {

  public function __construct(
    private readonly EntityTypeSchemaRegister $schemaRegister,
    private readonly EntityTypesRegister $typesRegister,
    private readonly EntityReferenceItemHandlerLocator $referenceItemHandlerLocator,
    private readonly GroupExporterRegister $groupExporterRegister,
  ) {}

  public function getEntityTypeConfiguration(string $entityType): array {
    $entityClass = $this->typesRegister->getEntityClassByEntityType($entityType);
    $schema = $this->schemaRegister->getSchemaFromEntityClass($entityClass);
    [$properties, $references, $categories] = $this->getProperties($schema);
    return [
      'entityType' => $entityType,
      'entityLabel' => $schema->getEntityLabel(),
      'properties' => $properties,
      'references' => $references,
    ];
  }

  private function getProperties(EntitySchema $schema): array {
    $properties = [];
    $references = [];
    $categories = [];
    [$defaultExporter, $exporterList] = $this->getGroupExporter();
    foreach ($schema->iterateProperties() as $name => $config) {
      $key = 'property' . '_' . $name;
      $categories[$key] = 'properties';
      $properties[$key] = [
        'type' => GroupTypes::PROPERTY,
        'key' => $key,
        'label' => $config->getLabel(),
        'description' => $config->getDescription(),
        'groupExporterList' => $exporterList,
        'defaultExporter' => $defaultExporter,
        'properties' => [
          'property' => $name,
          'cardinality' => $config->getCardinality(),
        ]
      ];
      $reference = $this->getEntityReference($config);
      if(!empty($reference)) {
        $references[$name] = $reference;
      }
    }

    return [$properties, $references, $categories];
  }

  private function getEntityReference(ItemConfigurationInterface $itemConfiguration): array {
    if(!$itemConfiguration->hasEntityReferenceHandler()) {
      return [];
    }

    $handler = $this->referenceItemHandlerLocator->getEntityReferenceHandlerFromItem($itemConfiguration);

    if(!$handler instanceof SimpleEntityReferenceJoinInterface) {
      return [];
    }

    [$referenceEntityClass, $property] = $handler->getTargetSetting($itemConfiguration);

    $schema = $this->schemaRegister->getSchemaFromEntityClass($referenceEntityClass);
    $referencedEntityType = $this->typesRegister->getEntityTypeByEntityClass($referenceEntityClass);

    return [
      'entityType' => $referencedEntityType,
      'entityLabel' => $schema->getEntityLabel(),
      'property' => $itemConfiguration->getItemName()
    ];
  }

  private function getGroupExporter(): array {
    return [
      'DefaultPropertyExporter',
      $this->groupExporterRegister->getGroupExporterList(GroupTypes::PROPERTY),
    ];
  }

}